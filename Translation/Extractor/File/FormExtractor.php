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
    private $logger;

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

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $this->inMethod = true;
            $this->localFormBuilderVars = array();

            foreach ($node->params as $param) {
                if (!$param->type instanceof \PHPParser_Node_Name) {
                    continue;
                }

                if ('Symfony\Component\Form\FormBuilder' !== $this->getFqcn($param->type->parts)) {
                    continue;
                }

                $this->localFormBuilderVars[$param->name] = true;
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
            // FIXME: Maybe we should throw an exception here?
            if (!$node->args[2]->value instanceof \PHPParser_Node_Expr_Array) {
                return;
            }

            foreach ($node->args[2]->value->items as $item) {
                if (!$item->key instanceof \PHPParser_Node_Scalar_String) {
                    continue;
                }

                if ('label' !== $item->key->value) {
                    continue;
                }

                // get doc comment
                $ignore = false;
                $desc = $meaning = null;
                if ($docComment = $item->value->getDocComment()) {
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
                        continue;
                    }

                    $message = sprintf('Unable to extract translation id for form label from non-string values, but got "%s" in %s on line %d. Please refactor your code to pass a string, or add "/** @Ignore */".', get_class($item->value), $this->file, $item->value->getLine());
                    if ($this->logger) {
                        $this->logger->err($message);

                        return;
                    }

                    throw new RuntimeException($message);
                }

                $message = new Message($item->value->value);
                $message->addSource(new FileSource((string) $this->file, $item->value->getLine()));

                if ($desc) {
                    $message->setDesc($desc);
                }

                if ($meaning) {
                    $message->setMeaning($meaning);
                }

                $this->catalogue->add($message);
            }
        }
    }

    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
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