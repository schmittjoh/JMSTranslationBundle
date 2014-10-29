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
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Annotation\Meaning;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Logger\LoggerAwareInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class AuthenticationMessagesExtractor implements LoggerAwareInterface, FileVisitorInterface, \PHPParser_NodeVisitor
{
    private $domain = 'authentication';
    private $traverser;
    private $file;
    private $catalogue;
    private $namespace = '';
    private $docParser;
    private $inAuthException = false;
    private $inGetMessageKey = false;
    private $logger;

    public function __construct(DocParser $parser)
    {
        $this->docParser = $parser;
        $this->traverser = new \PHPParser_NodeTraverser();
        $this->traverser->addVisitor($this);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->namespace = implode('\\', $node->name->parts);

            return;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Class) {
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

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            if ('getmessagekey' === strtolower($node->name)) {
                $this->inGetMessageKey = true;
            }

            return;
        }

        if (!$this->inGetMessageKey) {
            return;
        }

        if (!$node instanceof \PHPParser_Node_Stmt_Return) {
            return;
        }

        $ignore = false;
        $desc = $meaning = null;
        if ($docComment = $node->getDocComment()) {
            foreach ($this->docParser->parse($docComment->getText(), 'file '.$this->file.' near line '.$node->getLine()) as $annot) {
                if ($annot instanceof Ignore) {
                    $ignore = true;
                } else if ($annot instanceof Desc) {
                    $desc = $annot->text;
                } else if ($annot instanceof Meaning) {
                    $meaning = $annot->text;
                }
            }
        }

        if (!$node->expr instanceof \PHPParser_Node_Scalar_String) {
            if ($ignore) {
                return;
            }

            $message = sprintf('Could not extract id from return value, expected scalar string but got %s (in %s on line %d).', get_class($node->expr), $this->file, $node->expr->getLine());
            if ($this->logger) {
                $this->logger->err($message);

                return;
            }

            throw new RuntimeException($message);
        }

        $message = Message::create($node->expr->value, $this->domain)
            ->setDesc($desc)
            ->setMeaning($meaning)
            ->addSource(new FileSource((string) $this->file, $node->expr->getLine()))
        ;

        $this->catalogue->add($message);
    }

    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->namespace = '';
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Class) {
            $this->inAuthException = false;

            return;
        }

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $this->inGetMessageKey = false;

            return;
        }
    }

    public function beforeTraverse(array $nodes) { }
    public function afterTraverse(array $nodes) { }
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue) { }
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast) { }
}