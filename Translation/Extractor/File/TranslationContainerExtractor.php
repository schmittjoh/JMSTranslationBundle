<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\TranslationBundle\Translation\Extractor\File;

use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;

/**
 * Extracts translations from designated translation containers.
 *
 * For the purposes of this extractor, everything that implements the
 * TranslationContainerInterface is considered a translation container.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class TranslationContainerExtractor implements FileVisitorInterface, NodeVisitor
{
    /**
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * @var string
     */
    private $file;

    /**
     * @var MessageCatalogue
     */
    private $catalogue;

    /**
     * @var string
     */
    private $namespace = '';

    /**
     * @var array
     */
    private $useStatements = array();

    /**
     * TranslationContainerExtractor constructor.
     */
    public function __construct()
    {
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($this);
    }

    /**
     * @param Node $node
     * @return null|Node|void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            if (isset($node->name)) {
                $this->namespace = implode('\\', $node->name->parts);
            }
            $this->useStatements = array();

            return;
        }

        if ($node instanceof Node\Stmt\UseUse) {
            $nodeAliasName = is_string($node->alias) ? $node->alias : $node->getAlias()->name;
            $this->useStatements[$nodeAliasName] = implode('\\', $node->name->parts);

            return;
        }

        if (!$node instanceof Node\Stmt\Class_) {
            return;
        }

        $isContainer = false;
        foreach ($node->implements as $interface) {
            $name = implode('\\', $interface->parts);
            if (isset($this->useStatements[$name])) {
                $name = $this->useStatements[$name];
            }

            if ('JMS\TranslationBundle\Translation\TranslationContainerInterface' === $name) {
                $isContainer = true;
                break;
            }
        }

        if (!$isContainer) {
            return;
        }

        $messages = call_user_func(array($this->namespace.'\\'.$node->name, 'getTranslationMessages'));
        if (!is_array($messages)) {
            throw new RuntimeException(sprintf('%s::getTranslationMessages() was expected to return an array of messages, but got %s.', $this->namespace.'\\'.$node->name, gettype($messages)));
        }

        foreach ($messages as $message) {
            if (!$message instanceof Message) {
                throw new RuntimeException(sprintf('%s::getTranslationMessages() was expected to return an array of messages, but got an array which contains an item of type %s.', $this->namespace.'\\'.$node->name, gettype($message)));
            }

            $this->catalogue->add($message);
        }
    }

    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param array $ast
     */
    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    /**
     * @param array $nodes
     * @return void
     */
    public function beforeTraverse(array $nodes)
    {
    }

    /**
     * @param Node $node
     * @return void
     */
    public function leaveNode(Node $node)
    {
    }

    /**
     * @param array $nodes
     * @return void
     */
    public function afterTraverse(array $nodes)
    {
    }

    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     */
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue)
    {
    }

    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param \Twig_Node $ast
     */
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast)
    {
    }
}
