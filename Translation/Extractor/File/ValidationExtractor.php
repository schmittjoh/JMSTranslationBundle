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

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\MetadataFactoryInterface as LegacyMetadataFactoryInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;

/**
 * Extracts translations validation constraints.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ValidationExtractor implements FileVisitorInterface, NodeVisitor
{
    /**
     * @var ClassMetadataFactoryInterface|MetadataFactoryInterface|LegacyMetadataFactoryInterface
     */
    private $metadataFactory;

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
     * ValidationExtractor constructor.
     * @param $metadataFactory
     */
    public function __construct($metadataFactory)
    {
        if (! (
            $metadataFactory instanceof MetadataFactoryInterface
            || $metadataFactory instanceof LegacyMetadataFactoryInterface
            || $metadataFactory instanceof ClassMetadataFactoryInterface
        )) {
            throw new \InvalidArgumentException(sprintf('%s expects an instance of MetadataFactoryInterface or ClassMetadataFactoryInterface', get_class($this)));
        }
        $this->metadataFactory = $metadataFactory;

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($this);
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

        if (!$node instanceof Node\Stmt\Class_) {
            return;
        }

        $name = '' === $this->namespace ? $node->name : $this->namespace.'\\'.$node->name;

        if (!class_exists($name)) {
            return;
        }

        $metadata = ($this->metadataFactory instanceof ClassMetadataFactoryInterface)? $this->metadataFactory->getClassMetadata($name) : $this->metadataFactory->getMetadataFor($name);
        if (!$metadata->hasConstraints() && !count($metadata->getConstrainedProperties())) {
            return;
        }

        $this->extractFromConstraints($metadata->constraints);
        foreach ($metadata->members as $members) {
            foreach ($members as $member) {
                $this->extractFromConstraints($member->constraints);
            }
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
        $this->namespace = '';
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

    /**
     * @param array $constraints
     */
    private function extractFromConstraints(array $constraints)
    {
        foreach ($constraints as $constraint) {
            $ref = new \ReflectionClass($constraint);
            $defaultValues = $ref->getDefaultProperties();

            $properties = $ref->getProperties();

            foreach ($properties as $property) {
                $propName = $property->getName();

                // If the property ends with 'Message'
                if (strtolower(substr($propName, -1 * strlen('Message'))) === 'message') {
                    // If it is different from the default value
                    if ($defaultValues[$propName] !== $constraint->{$propName}) {
                        $message = new Message($constraint->{$propName}, 'validators');
                        $this->catalogue->add($message);
                    }
                }
            }
        }
    }
}
