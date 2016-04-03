<?php

namespace JMS\TranslationBundle\Translation\Extractor\File;

use JMS\TranslationBundle\Logger\LoggerAwareInterface;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PhpParser\Node;
use Psr\Log\LoggerInterface;

class AbstractFileExtractor implements FileVisitorInterface, LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $nodes
     * @return null|\PhpParser\Node[]|void
     */
    public function beforeTraverse(array $nodes)
    {
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
    public function afterTraverse(array $nodes)
    {
    }

    /**
     * @inheritDoc
     */
    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue)
    {
    }

    /**
     * @inheritDoc
     */
    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
    }

    /**
     * @inheritDoc
     */
    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $ast)
    {
    }
}
