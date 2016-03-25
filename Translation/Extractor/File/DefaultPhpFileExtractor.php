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
use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Annotation\Meaning;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Logger\LoggerAwareInterface;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Node\Scalar\String_;
use Psr\Log\LoggerInterface;

/**
 * This parser can extract translation information from PHP files.
 *
 * It parses all calls that are made to a method named "trans".
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class DefaultPhpFileExtractor implements LoggerAwareInterface, FileVisitorInterface, NodeVisitor
{
    private $traverser;
    private $catalogue;
    private $file;
    private $docParser;

    /**
     * @var LoggerInterface
     */
    private $logger;
    private $previousNode;

    public function __construct(DocParser $docParser)
    {
        $this->docParser = $docParser;
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

    public function enterNode(Node $node)
    {

        if (!$node instanceof Node\Expr\MethodCall
            || !is_string($node->name)
            || ('trans' !== strtolower($node->name) && 'transchoice' !== strtolower($node->name))) {

            $this->previousNode = $node;
            return;
        }

        $ignore = false;
        $desc = $meaning = null;
        if (null !== $docComment = $this->getDocCommentForNode($node)) {
            if ($docComment instanceof Doc) {
                $docComment = $docComment->getText();
            }
            foreach ($this->docParser->parse($docComment, 'file '.$this->file.' near line '.$node->getLine()) as $annot) {
                if ($annot instanceof Ignore) {
                    $ignore = true;
                } else if ($annot instanceof Desc) {
                    $desc = $annot->text;
                } else if ($annot instanceof Meaning) {
                    $meaning = $annot->text;
                }
            }
        }

        if (!$node->args[0]->value instanceof String_) {
            if ($ignore) {
                return;
            }

            $message = sprintf('Can only extract the translation id from a scalar string, but got "%s". Please refactor your code to make it extractable, or add the doc comment /** @Ignore */ to this code element (in %s on line %d).', get_class($node->args[0]->value), $this->file, $node->args[0]->value->getLine());

            if ($this->logger) {
                $this->logger->error($message);
                return;
            }

            throw new RuntimeException($message);
        }

        $id = $node->args[0]->value->value;

        $index = 'trans' === strtolower($node->name) ? 2 : 3;
        if (isset($node->args[$index])) {
            if (!$node->args[$index]->value instanceof String_) {
                if ($ignore) {
                    return;
                }

                $message = sprintf('Can only extract the translation domain from a scalar string, but got "%s". Please refactor your code to make it extractable, or add the doc comment /** @Ignore */ to this code element (in %s on line %d).', get_class($node->args[0]->value), $this->file, $node->args[0]->value->getLine());

                if ($this->logger) {
                    $this->logger->error($message);
                    return;
                }

                throw new RuntimeException($message);
            }

            $domain = $node->args[$index]->value->value;
        } else {
            $domain = 'messages';
        }

        $message = new Message($id, $domain);
        $message->setDesc($desc);
        $message->setMeaning($meaning);
        $message->addSource(new FileSource((string) $this->file, $node->getLine()));

        $this->catalogue->add($message);
    }

    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    public function beforeTraverse(array $nodes) { }
    public function leaveNode(Node $node) { }
    public function afterTraverse(array $nodes) { }
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue) { }
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast) { }

    private function getDocCommentForNode(Node $node)
    {
        // check if there is a doc comment for the ID argument
        // ->trans(/** @Desc("FOO") */ 'my.id')
        if (null !== $comment = $node->args[0]->getDocComment()) {
            return $comment->getText();
        }

        // this may be placed somewhere up in the hierarchy,
        // -> /** @Desc("FOO") */ trans('my.id')
        // /** @Desc("FOO") */ ->trans('my.id')
        // /** @Desc("FOO") */ $translator->trans('my.id')
        if (null !== $comment = $node->getDocComment()) {
            return $comment->getText();
        } elseif (null !== $this->previousNode && $this->previousNode->getDocComment() !== null) {
            $comment = $this->previousNode->getDocComment();
            return is_object($comment) ? $comment->getText() : $comment;
        }

        return null;
    }
}
