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
use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use JMS\TranslationBundle\Logger\LoggerAwareInterface;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use Psr\Log\LoggerInterface;

class FormExtractor implements FileVisitorInterface, LoggerAwareInterface, NodeVisitor
{
    /**
     * @var DocParser
     */
    private $docParser;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $defaultDomain;

    /**
     * @var string
     */
    private $defaultDomainMessages;

    /**
     * FormExtractor constructor.
     * @param DocParser $docParser
     */
    public function __construct(DocParser $docParser)
    {
        $this->docParser = $docParser;

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($this);
    }

    /**
     * @param Node $node
     * @return null
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $this->defaultDomain = null;
            $this->defaultDomainMessages = array();
        }

        if ($node instanceof Node\Expr\MethodCall) {
            if (!is_string($node->name)) {
                return;
            }

            $name = strtolower($node->name);
            if ('setdefaults' === $name || 'replacedefaults' === $name) {
                $this->parseDefaultsCall($node);
                return;
            }
        }

        if ($node instanceof Node\Expr\Array_) {
            // first check if a translation_domain is set for this field
            $domain = null;
            foreach ($node->items as $item) {
                if (!$item->key instanceof Node\Scalar\String_) {
                    continue;
                }

                if ('translation_domain' === $item->key->value) {
                    if (!$item->value instanceof Node\Scalar\String_) {
                        continue;
                    }

                    $domain = $item->value->value;
                }
            }

            // look for options containing a message
            foreach ($node->items as $item) {
                if (!$item->key instanceof Node\Scalar\String_) {
                    continue;
                }

                if ('empty_value' === $item->key->value && $item->value instanceof Node\Expr\ConstFetch
                    && $item->value->name instanceof Node\Name && 'false' === $item->value->name->parts[0]) {
                    continue;
                }
                if ('empty_value' === $item->key->value && $item->value instanceof Node\Expr\Array_) {
                    foreach ($item->value->items as $sitem) {
                        $this->parseItem($sitem, $domain);
                    }
                    continue;
                }

                if ('choices' === $item->key->value && !$item->value instanceof Node\Expr\Array_) {
                    continue;
                }

                if ('label' !== $item->key->value && 'empty_value' !== $item->key->value && 'choices' !== $item->key->value && 'invalid_message' !== $item->key->value && 'attr' !== $item->key->value) {
                    continue;
                }

                if ('choices' === $item->key->value) {
                    foreach ($item->value->items as $sitem) {
                        $this->parseItem($sitem, $domain);
                    }
                } elseif ('attr' === $item->key->value && is_array($item->value->items)) {
                    foreach ($item->value->items as $sitem) {
                        if ('placeholder' == $sitem->key->value) {
                            $this->parseItem($sitem, $domain);
                        }
                        if ('title' == $sitem->key->value) {
                            $this->parseItem($sitem, $domain);
                        }
                    }
                } elseif ('invalid_message' === $item->key->value) {
                    $this->parseItem($item, 'validators');
                } else {
                    $this->parseItem($item, $domain);
                }
            }
        }
    }

    /**
     * @param Node $node
     */
    private function parseDefaultsCall(Node $node)
    {
        static $returningMethods = array(
            'setdefaults' => true, 'replacedefaults' => true, 'setoptional' => true, 'setrequired' => true,
            'setallowedvalues' => true, 'addallowedvalues' => true, 'setallowedtypes' => true,
            'addallowedtypes' => true, 'setfilters' => true
        );

        $var = $node->var;
        while ($var instanceof Node\Expr\MethodCall) {
            if (!isset($returningMethods[strtolower($var->name)])) {
                return;
            }

            $var = $var->var;
        }

        if (!$var instanceof Node\Expr\Variable) {
            return;
        }

        // check if options were passed
        if (!isset($node->args[0])) {
            return;
        }

        // ignore everything except an array
        if (!$node->args[0]->value instanceof Node\Expr\Array_) {
            return;
        }

        // check if a translation_domain is set as a default option
        $domain = null;
        foreach ($node->args[0]->value->items as $item) {
            if (!$item->key instanceof Node\Scalar\String_) {
                continue;
            }

            if ('translation_domain' === $item->key->value) {
                if (!$item->value instanceof Node\Scalar\String_) {
                    continue;
                }

                $this->defaultDomain = $item->value->value;
            }
        }
    }

    /**
     * @param $item
     * @param null $domain
     */
    private function parseItem($item, $domain = null)
    {
        // get doc comment
        $ignore = false;
        $desc = $meaning = $docComment = null;

        if ($item->key) {
            $docComment = $item->key->getDocComment();
        }

        if (!$docComment) {
            $docComment = $item->value->getDocComment();
        }

        $docComment = is_object($docComment) ? $docComment->getText() : null;

        if ($docComment) {
            if ($docComment instanceof Doc) {
                $docComment = $docComment->getText();
            }
            foreach ($this->docParser->parse($docComment, 'file '.$this->file.' near line '.$item->value->getLine()) as $annot) {
                if ($annot instanceof Ignore) {
                    $ignore = true;
                } elseif ($annot instanceof Desc) {
                    $desc = $annot->text;
                } elseif ($annot instanceof Meaning) {
                    $meaning = $annot->text;
                }
            }
        }

        // check if the value is explicitly set to false => e.g. for FormField that should be rendered without label
        $ignore = $ignore || !$item->value instanceof Node\Scalar\String_ || $item->value->value == false;

        if (!$item->value instanceof Node\Scalar\String_ && !$item->value instanceof Node\Scalar\LNumber) {
            if ($ignore) {
                return;
            }

            $message = sprintf('Unable to extract translation id for form label/title/placeholder from non-string values, but got "%s" in %s on line %d. Please refactor your code to pass a string, or add "/** @Ignore */".', get_class($item->value), $this->file, $item->value->getLine());
            if ($this->logger) {
                $this->logger->error($message);

                return;
            }

            throw new RuntimeException($message);
        }

        $source = new FileSource((string) $this->file, $item->value->getLine());
        $id = $item->value->value;

        if (null === $domain) {
            $this->defaultDomainMessages[] = array(
                'id' => $id,
                'source' => $source,
                'desc' => $desc,
                'meaning' => $meaning
            );
        } else {
            $this->addToCatalogue($id, $source, $domain, $desc, $meaning);
        }
    }

    /**
     * @param string $id
     * @param string $source
     * @param null|string $domain
     * @param null|string $desc
     * @param null|string $meaning
     */
    private function addToCatalogue($id, $source, $domain = null, $desc = null, $meaning = null)
    {
        if (null === $domain) {
            $message = new Message($id);
        } else {
            $message = new Message($id, $domain);
        }

        $message->addSource($source);

        if ($desc) {
            $message->setDesc($desc);
        }

        if ($meaning) {
            $message->setMeaning($meaning);
        }

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
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);

        if ($this->defaultDomainMessages) {
            foreach ($this->defaultDomainMessages as $message) {
                $this->addToCatalogue($message['id'], $message['source'], $this->defaultDomain, $message['desc'], $message['meaning']);
            }
        }
    }

    /**
     * @param Node $node
     * @return null|\PhpParser\Node[]|void
     */
    public function leaveNode(Node $node)
    {
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

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
