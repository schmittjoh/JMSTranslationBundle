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

/**
 * Extracts translations from designated translation containers.
 *
 * For the purposes of this extractor, everything that implements the
 * TranslationContainerInterface is considered a translation container.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class TranslationContainerExtractor implements FileVisitorInterface, \PHPParser_NodeVisitor
{
    private $traverser;
    private $file;
    private $catalogue;
    private $namespace = '';
    private $useStatements = array();

    public function __construct()
    {
        $this->traverser = new \PHPParser_NodeTraverser();
        $this->traverser->addVisitor($this);
    }

    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->namespace = implode('\\', $node->name->parts);
            $this->useStatements = array();

            return;
        }

        if ($node instanceof \PHPParser_Node_Stmt_UseUse) {
            $this->useStatements[$node->alias] = implode('\\', $node->name->parts);

            return;
        }

        if (!$node instanceof \PHPParser_Node_Stmt_Class) {
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

    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    public function beforeTraverse(array $nodes) { }
    public function leaveNode(\PHPParser_Node $node) { }
    public function afterTraverse(array $nodes) { }
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue) { }
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast) { }
}