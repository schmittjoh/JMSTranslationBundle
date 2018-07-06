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
use JMS\TranslationBundle\Annotation\Meaning;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Logger\LoggerAwareInterface;
use JMS\TranslationBundle\Translation\FileSourceFactory;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use Psr\Log\LoggerInterface;

class AuthenticationMessagesExtractor implements LoggerAwareInterface, FileVisitorInterface, NodeVisitor
{
    /**
     * @var FileSourceFactory
     */
    private $fileSourceFactory;

    /**
     * @var string
     */
    private $domain = 'authentication';

    /**
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * @var \SplFileInfo
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
     * @var DocParser
     */
    private $docParser;

    /**
     * @var bool
     */
    private $inAuthException = false;

    /**
     * @var bool
     */
    private $inGetMessageKey = false;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AuthenticationMessagesExtractor constructor.
     * @param DocParser $parser
     * @param FileSourceFactory $fileSourceFactory
     */
    public function __construct(DocParser $parser, FileSourceFactory $fileSourceFactory)
    {
        $this->docParser = $parser;
        $this->fileSourceFactory = $fileSourceFactory;
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($this);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param Node $node
     * @return void
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            if (isset($node->name)) {
                $this->namespace = implode('\\', $node->name->parts);
            }

            return;
        }

        if ($node instanceof Node\Stmt\Class_) {
            $name = '' === $this->namespace ? $node->name : $this->namespace.'\\'.$node->name;

            if (!class_exists($name)) {
                return;
            }
            $ref = new \ReflectionClass($name);

            if (!$ref->isSubclassOf('Symfony\Component\Security\Core\Exception\AuthenticationException')
                && $ref->name !== 'Symfony\Component\Security\Core\Exception\AuthenticationException') {
                return;
            }

            if (!$ref->hasMethod('getMessageKey')) {
                return;
            }
            $this->inAuthException = true;

            return;
        }

        if (!$this->inAuthException) {
            return;
        }

        if ($node instanceof Node\Stmt\ClassMethod) {
            if ('getmessagekey' === strtolower($node->name)) {
                $this->inGetMessageKey = true;
            }

            return;
        }

        if (!$this->inGetMessageKey) {
            return;
        }

        if (!$node instanceof Node\Stmt\Return_) {
            return;
        }

        $ignore = false;
        $desc = $meaning = null;
        if ($docComment = $node->getDocComment()) {
            foreach ($this->docParser->parse($docComment->getText(), 'file '.$this->file.' near line '.$node->getLine()) as $annot) {
                if ($annot instanceof Ignore) {
                    $ignore = true;
                } elseif ($annot instanceof Desc) {
                    $desc = $annot->text;
                } elseif ($annot instanceof Meaning) {
                    $meaning = $annot->text;
                }
            }
        }

        if (!$node->expr instanceof Node\Scalar\String_) {
            if ($ignore) {
                return;
            }

            $message = sprintf('Could not extract id from return value, expected scalar string but got %s (in %s on line %d).', get_class($node->expr), $this->file, $node->expr->getLine());
            if ($this->logger) {
                $this->logger->error($message);

                return;
            }

            throw new RuntimeException($message);
        }

        $message = Message::create($node->expr->value, $this->domain)
            ->setDesc($desc)
            ->setMeaning($meaning)
            ->addSource($this->fileSourceFactory->create($this->file, $node->expr->getLine()))
        ;

        $this->catalogue->add($message);
    }

    /**
     * @param \SplFileInfo $file
     * @param MessageCatalogue $catalogue
     * @param array $ast
     */
    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->namespace = '';
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    /**
     * @param Node $node
     * @return false|null|Node|\PhpParser\Node[]|void
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $this->inAuthException = false;

            return;
        }

        if ($node instanceof Node\Stmt\ClassMethod) {
            $this->inGetMessageKey = false;

            return;
        }
    }

    /**
     * @param array $nodes
     * @return null|\PhpParser\Node[]|void
     */
    public function beforeTraverse(array $nodes)
    {
    }

    /**
     * @param array $nodes
     * @return null|\PhpParser\Node[]|void
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
