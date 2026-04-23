<?php

declare(strict_types=1);

/*
 * Copyright 2016 Arturs Vonda <open-source@artursvonda.lv>
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

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Model\SourceInterface;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use JMS\TranslationBundle\Translation\FileSourceFactory;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use SplFileInfo;
use Twig\Node\Node as TwigNode;

/**
 * Extracts
 */
class ValidationContextExtractor implements FileVisitorInterface, NodeVisitor
{
    /**
     * @var NodeTraverser
     */
    private $traverser;
    /**
     * @var array
     */
    private $messages = [];
    /**
     * @var MessageCatalogue
     */
    private $catalogue;
    /**
     * @var SplFileInfo
     */
    private $file;
    /**
     * @var array
     */
    private $aliases = [];
    /**
     * @var string
     */
    private $contextVariable;
    /**
     * @var string|null
     */
    private $domain;
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var FileSource|null
     */
    private $source;
    private $fileSourceFactory;

    public function __construct(FileSourceFactory $fileSourceFactory)
    {
        $this->fileSourceFactory = $fileSourceFactory;
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($this);
    }

    /**
     * {@inheritdoc}
     */
    public function visitFile(SplFileInfo $file, MessageCatalogue $catalogue)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function visitPhpFile(SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->messages = [];
        $this->traverser->traverse($ast);

        foreach ($this->messages as $message) {
            $this->addToCatalogue($message['id'], $message['source'], $message['domain']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function visitTwigFile(SplFileInfo $file, MessageCatalogue $catalogue, TwigNode $ast)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function beforeTraverse(array $nodes)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->aliases = [];

            return;
        }

        if ($node instanceof Node\Stmt\Use_) {
            foreach ($node->uses as $use) {
                $alias = $use->alias ?: $use->getAlias()->name;
                $this->aliases[$alias] = (string) $use->name;
            }

            return;
        }

        if ($node instanceof Node\Stmt\ClassMethod) {
            $params = $node->getParams();
            if (!count($params)) {
                return;
            }
            $param1 = $params[0];
            $paramClass = $this->resolveAlias((string) $param1->type);
            if (is_subclass_of($paramClass, '\Symfony\Component\Validator\Context\ExecutionContextInterface')) {
                // For BC, we try first $name property and then newer $var->name prop
                // This can be changed to just $param1->var->name when we support only php-parser:^4
                $this->contextVariable = !empty($param1->name) ? $param1->name : $param1->var->name;
            }

            return;
        }

        if ($node instanceof Node\Expr\MethodCall) {
            $this->parseMethodCall($node);
        }
    }

    private function parseMethodCall(Node\Expr\MethodCall $node)
    {
        if (!$this->contextVariable) {
            return;
        }

        if ($node->var instanceof Node\Expr\MethodCall) {
            $this->parseMethodCall($node->var);
        }

        // Cast to string to make compatible with both pre-v4 and post-v4 php-parser
        $name = (string) $node->name;

        if ($name === 'buildViolation') {
            $this->id = null;
            $this->domain = null;

            if ($node->args) {
                $arg1 = $node->args[0];
                if ($arg1->value instanceof Node\Scalar\String_) {
                    $this->id = $arg1->value->value;
                    $this->source = $this->fileSourceFactory->create($this->file, $arg1->value->getLine());
                }
            }
        } elseif ($name === 'setTranslationDomain') {
            if ($node->args) {
                $arg1 = $node->args[0];
                if ($arg1->value instanceof Node\Scalar\String_) {
                    $this->domain = $arg1->value->value;
                }
            }
        } elseif ($name === 'addViolation') {
            if ($this->id && $this->source) {
                $this->messages[] = [
                    'id' => $this->id,
                    'source' => $this->source,
                    'domain' => $this->domain,
                ];
            }

            $this->id = null;
            $this->domain = null;
            $this->source = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassMethod) {
            $this->contextVariable = null;
            $this->domain = null;
            $this->id = null;
            $this->source = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterTraverse(array $nodes)
    {
    }

    /**
     * @param string $id
     * @param SourceInterface $source
     * @param string|null $domain
     */
    private function addToCatalogue($id, SourceInterface $source, $domain = null)
    {
        if (null === $domain) {
            $message = new Message($id);
        } else {
            $message = new Message($id, $domain);
        }

        $message->addSource($source);

        $this->catalogue->add($message);
    }

    /**
     * @return string
     */
    private function resolveAlias(string $class)
    {
        return $this->aliases[$class] ?? $class;
    }
}
