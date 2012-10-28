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
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class FormExtractor implements FileVisitorInterface, \PHPParser_NodeVisitor
{
    private $docParser;
    private $traverser;
    private $file;
    private $catalogue;
    private $namespace = '';
    private $useStatements = array();
    private $inMethod = false;
    private $localFormBuilderVars;
    private $localOptionResolverVars;
    private $logger;
    private $defaultDomain;
    private $defaultDomainMessages;

    public function __construct(DocParser $docParser)
    {
        $this->docParser = $docParser;

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

        if ($node instanceof \PHPParser_Node_Stmt_Class) {
            $this->defaultDomain = null;
            $this->defaultDomainMessages = array();
        }

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $this->inMethod = true;
            $this->localFormBuilderVars = array();
            $this->localOptionResolverVars = array();

            foreach ($node->params as $param) {
                if (!$param->type instanceof \PHPParser_Node_Name) {
                    continue;
                }

                $fqcn = $this->getFqcn($param->type->parts);
                if ('Symfony\Component\Form\FormBuilder' ===  $fqcn ||
                    'Symfony\Component\Form\FormBuilderInterface' === $fqcn) {
                    $this->localFormBuilderVars[$param->name] = true;
                } elseif ('Symfony\Component\OptionsResolver\OptionsResolverInterface' === $fqcn) {
                    $this->localOptionResolverVars[$param->name] = true;
                }
            }
            return;
        }

        // the following is only relevant while in a method
        if(!$this->inMethod) {
            return;
        }

        if ($node instanceof \PHPParser_Node_Expr_MethodCall) {
            if (!is_string($node->name)) {
                return;
            }

            $name = strtolower($node->name);
            if ('setdefaults' === $name || 'replacedefaults' === $name) {
                $this->parseDefaultsCall($name, $node);
                return;
            }

            if ('add' !== $name = strtolower($node->name)) {
                return;
            }

            static $returningMethods = array(
                'setdatamapper' => true, 'settypes' => true, 'setdata' => true, 'setreadonly' => true,
                'setrequired' => true, 'seterrorbubbling' => true, 'addvalidator' => true,
                'addeventlistener' => true, 'addeventsubscriber' => true, 'appendnormtransformer' => true,
                'prependnormtransformer' => true, 'resetnormtransformers' => true, 'appendclienttransformer' => true,
                'prependclienttransformer' => true, 'resetclienttransformers' => true, 'setattribute' => true,
                'setemptydata' => true, 'add' => true, 'remove' => true,
            );

            $var = $node->var;
            while ($var instanceof \PHPParser_Node_Expr_MethodCall) {
                if (!isset($returningMethods[strtolower($var->name)])) {
                    return;
                }

                $var = $var->var;
            }

            if (!$var instanceof \PHPParser_Node_Expr_Variable) {
                return;
            }

            if (!isset($this->localFormBuilderVars[$var->name])) {
                return;
            }

            // check if options were passed
            if (!isset($node->args[2])) {
                return;
            }

            // ignore everything except an array
            if (!$node->args[2]->value instanceof \PHPParser_Node_Expr_Array) {
                return;
            }

            // first check if a translation_domain is set for this field
            $domain = null;
            foreach ($node->args[2]->value->items as $item) {
                if (!$item->key instanceof \PHPParser_Node_Scalar_String) {
                    continue;
                }

                if ('translation_domain' === $item->key->value) {
                    if (!$item->value instanceof \PHPParser_Node_Scalar_String) {
                        continue;
                    }

                    $domain = $item->value->value;
                }
            }

            // look for options containing a message
            foreach ($node->args[2]->value->items as $item) {
                if (!$item->key instanceof \PHPParser_Node_Scalar_String) {
                    continue;
                }

                if ('empty_value' === $item->key->value && $item->value instanceof \PHPParser_Node_Expr_ConstFetch
                    && $item->value->name instanceof \PHPParser_Node_Name && 'false' === $item->value->name->parts[0]) {
                	continue;
                }

                if ('choices' === $item->key->value && !$item->value instanceof \PHPParser_Node_Expr_Array) {
                    continue;
                }

                if ('first_options' === $item->key->value && !$item->value instanceof \PHPParser_Node_Expr_Array) {
                    continue;
                }

                if ('second_options' === $item->key->value && !$item->value instanceof \PHPParser_Node_Expr_Array) {
                    continue;
                }

                if ('label' !== $item->key->value && 'empty_value' !== $item->key->value && 'choices' !== $item->key->value && 'first_options' !== $item->key->value && 'second_options' !== $item->key->value && 'invalid_message' !== $item->key->value) {
                    continue;
                }

                if ('choices' === $item->key->value) {
                    foreach ($item->value->items as $sitem) {
                        $this->parseItem($sitem, $domain);
                    }
                } elseif ('first_options' === $item->key->value || 'second_options' === $item->key->value) {
                    foreach ($item->value->items as $sitem) {
                        if ('label' == $sitem->key->value) {
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

    private function parseDefaultsCall($name, \PHPParser_Node $node)
    {
        static $returningMethods = array(
            'setdefaults' => true, 'replacedefaults' => true, 'setoptional' => true, 'setrequired' => true,
            'setallowedvalues' => true, 'addallowedvalues' => true, 'setallowedtypes' => true,
            'addallowedtypes' => true, 'setfilters' => true
        );

        $var = $node->var;
        while ($var instanceof \PHPParser_Node_Expr_MethodCall) {
            if (!isset($returningMethods[strtolower($var->name)])) {
                return;
            }

            $var = $var->var;
        }


        if (!$var instanceof \PHPParser_Node_Expr_Variable) {
            return;
        }

        if (!isset($this->localOptionResolverVars[$var->name])) {
            return;
        }

        // check if options were passed
        if (!isset($node->args[0])) {
            return;
        }

        // ignore everything except an array
        if (!$node->args[0]->value instanceof \PHPParser_Node_Expr_Array) {
            return;
        }

        // check if a translation_domain is set as a default option
        $domain = null;
        foreach ($node->args[0]->value->items as $item) {
            if (!$item->key instanceof \PHPParser_Node_Scalar_String) {
                continue;
            }

            if ('translation_domain' === $item->key->value) {
                if (!$item->value instanceof \PHPParser_Node_Scalar_String) {
                    continue;
                }

                $this->defaultDomain = $item->value->value;
            }
        }

    }

    private function parseItem($item, $domain = null)
    {
        // get doc comment
        $ignore = false;
        $desc = $meaning = null;
        $docComment = $item->key->getDocComment();
        $docComment = $docComment ? $docComment : $item->value->getDocComment();
        if ($docComment) {
            foreach ($this->docParser->parse($docComment, 'file '.$this->file.' near line '.$item->value->getLine()) as $annot) {
                if ($annot instanceof Ignore) {
                    $ignore = true;
                } else if ($annot instanceof Desc) {
                    $desc = $annot->text;
                } else if ($annot instanceof Meaning) {
                    $meaning = $annot->text;
                }
            }
        }

        if (!$item->value instanceof \PHPParser_Node_Scalar_String) {
            if ($ignore) {
                return;
            }

            $message = sprintf('Unable to extract translation id for form label from non-string values, but got "%s" in %s on line %d. Please refactor your code to pass a string, or add "/** @Ignore */".', get_class($item->value), $this->file, $item->value->getLine());
            if ($this->logger) {
                $this->logger->err($message);

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

    public function leaveNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_ClassMethod) {
            $this->inMethod = false;

            return;
        }
    }

    public function beforeTraverse(array $nodes) { }
    public function afterTraverse(array $nodes) { }
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue) { }
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast) { }

    private function getFqcn(array $parts)
    {
        $alias = array_shift($parts);

        if ('\\' === $alias[0]) {
            return implode('\\', $parts);
        }

        if (isset($this->useStatements[$alias])) {
            return $this->useStatements[$alias];
        }

        return $this->namespace.'\\'.implode('\\', $parts);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
