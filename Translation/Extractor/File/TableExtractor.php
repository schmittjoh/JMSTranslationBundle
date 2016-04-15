<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 * 
 * Added by Nicky Gerritsen in march 2015 for the StreamOne manager
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
use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use Psr\Log\LoggerInterface;

class TableExtractor implements FileVisitorInterface, NodeVisitor
{
    private $docParser;
    private $traverser;
    private $file;
    private $catalogue;
    private $logger;

    /**
     * @var \PHPParser_Node
     *   The last node that contained a comment
     */
    private $last_node_with_comment;

    public function __construct(DocParser $docParser)
    {
        $this->docParser = $docParser;

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($this);
    }


    public function enterNode(Node $node)
    {
        if ($node->getDocComment() !== null)
        {
            $this->last_node_with_comment = $node;
        }
        
        if ($node instanceof MethodCall) {

            if (!is_string($node->name)) {
                return;
            }

            $name = strtolower($node->name);
            if ('addcolumn' === $name) {
                $this->parseNode($node);
            }
        }
        
        // Find iocn definitions
        if ($node instanceof Node\Expr\Array_) {
            // Icon definitions contain an 'icon' and 'title' array-key
            $icon_element = $this->findArrayItemWithName($node, 'icon');
            $title_element = $this->findArrayItemWithName($node, 'title');
            
            if ($icon_element !== null && $title_element !== null) {
                // Title should be translated

                $desc = $meaning = null;
                
                if ($this->last_node_with_comment !== null) {
                    // Parse doc-comment
                    list($ignore, $desc, $meaning) = $this->getDocCommentData($this->last_node_with_comment);

                    // Unset used doccomment node
                    $this->last_node_with_comment = null;

                    if ($ignore) {
                        return;
                    }
                }

                $source = new FileSource((string)$this->file, $title_element->getLine());
                $value = $title_element->value;

                $this->addToCatalogue($value, $source, $desc, $meaning);
            }
        }
    }

    private function parseNode(MethodCall $node)
    {
        // Add translation for column name
        if (count($node->args) >= 1) {
            $first_argument = $node->args[0];

            list($ignore, $desc, $meaning) = $this->getDocCommentData($first_argument->value);
            if ($ignore) {
                return;
            }

            if (!$first_argument->value instanceof Node\Scalar\String_) {

                $message = sprintf('Unable to extract translation id for table column name from non-string values, but got "%s" in %s on line %d. Please refactor your code to pass a string, or add "/** @Ignore */".', get_class($first_argument->value), $this->file, $first_argument->value->getLine());
                if ($this->logger) {
                    $this->logger->err($message);

                    return;
                }

                throw new RuntimeException($message);
            }

            $source = new FileSource((string)$this->file, $first_argument->value->getLine());
            $value = $first_argument->value->value;

            if (empty($value)) {
                // Empty columns should not be translated
                return;
            }

            $this->addToCatalogue($value, $source, $desc, $meaning);
        }

        // Add translation for dropdown link names
        if (count($node->args) >= 3) {
            $third_argument = $node->args[2];
            if ($third_argument->value instanceof Node\Expr\Array_) {
                $array_argument = $third_argument->value;
                $formatter = $this->findArrayItemWithName($array_argument, 'formatter');
                if ($formatter instanceof Node\Expr\Array_) {
                    $this->parseFormatterNode($formatter);
                }
            }
        }
    }
    
    private function getDocCommentData(Node $node)
    {
        $ignore = false;
        $desc = $meaning = null;
        $docComment = $node->getDocComment();

        if ($docComment) {
            if ($docComment instanceof Doc) {
                $docComment = $docComment->getText();
            }
            foreach ($this->docParser->parse($docComment, 'file ' . $this->file . ' near line ' . $node->getLine()) as $annot) {
                if ($annot instanceof Ignore) {
                    $ignore = true;
                } else if ($annot instanceof Desc) {
                    $desc = $annot->text;
                } else if ($annot instanceof Meaning) {
                    $meaning = $annot->text;
                }
            }
        }

        return [$ignore, $desc, $meaning];
    }
    
    private function parseFormatterNode(Node\Expr\Array_ $formatter)
    {
        $formatter_name = $this->findArrayItemWithName($formatter, 'formatter');
        if ($formatter_name instanceof Node\Scalar\String_ && $formatter_name->value == 'dropdown') {
            $links = $this->findArrayItemWithName($formatter, 'links');
            if ($links instanceof Node\Expr\Array_) {
                foreach ($links->items as $item) {
                    if ($item->key instanceof Node\Scalar\String_) {
                        list($ignore, $desc, $meaning) = $this->getDocCommentData($item->key);
                        if ($ignore) {
                            return;
                        }

                        $source = new FileSource((string)$this->file, $item->key->getLine());
                        $value = $item->key->value;

                        $this->addToCatalogue($value, $source, $desc, $meaning);
                    }
                }
            }
        }
    }
    
    private function findArrayItemWithName(Node\Expr\Array_ $array, $name)
    {
        foreach ($array->items as $item)
        {
            if ($item->key instanceof Node\Scalar\String_ && $item->key->value == $name)
            {
                return $item->value;
            }
        }
        
        return null;
    }

    private function addToCatalogue($id, $source, $desc = null, $meaning = null)
    {
        $message = new Message($id);
        
        $message->addSource($source);

        if ($desc) {
            $message->setDesc($desc);
        }

        if ($meaning) {
            $message->setMeaning($meaning);
        }

        $this->catalogue->add($message);
    }

    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    public function leaveNode(Node $node) { }

    public function beforeTraverse(array $nodes) { }
    public function afterTraverse(array $nodes) { }
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue) { }
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast) { }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
