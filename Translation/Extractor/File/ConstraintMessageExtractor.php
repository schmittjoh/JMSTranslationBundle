<?php

namespace JMS\TranslationBundle\Translation\Extractor\File;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use Symfony\Component\Validator\Constraint;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;

class ConstraintMessageExtractor implements FileVisitorInterface, NodeVisitor
{

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
     */
    public function __construct()
    {
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

        if ($node instanceof Node\Stmt\Class_) {
            if (!class_exists($name)) {
                return;
            }
            $ref = new \ReflectionClass($name);
            if (!$ref->isSubclassOf('Symfony\Component\Validator\Constraint')) {
                return;
            } else {
                $constraint = $ref->newInstance();
                /** @var Constraint $constraint */
                $this->extractFromConstraint($constraint);
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
     * @param Constraint $constraint
     */
    private function extractFromConstraint(Constraint $constraint)
    {
        $ref = new \ReflectionClass($constraint);

        $properties = $ref->getProperties();

        foreach ($properties as $property) {
            $propName = $property->getName();

            // If the property ends with 'Message'
            if (strtolower(substr($propName, -1 * strlen('Message'))) === 'message') {
                $message = new Message($constraint->{$propName}, 'validators');
                $this->catalogue->add($message);
            }
        }
    }
}