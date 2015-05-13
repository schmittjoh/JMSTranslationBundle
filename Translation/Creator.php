<?php

namespace JMS\TranslationBundle\Translation;


use JMS\TranslationBundle\Translation\Config;
use JMS\TranslationBundle\Translation\ExtractorManager;
use JMS\TranslationBundle\Translation\FileWriter;
use JMS\TranslationBundle\Translation\LoaderManager;
use JMS\TranslationBundle\Util\FileUtils;
use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\Comparison\CatalogueComparator;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Translation\MessageCatalogue as SymfonyMessageCatalogue;
use Symfony\Component\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * Wrapper around the different components.
 *
 * This class ties the different components together, and is responsible for
 * creating new messages in the message catalogue, and persisting them
 */
class Creator
{
    /**
     * @var LoaderManager
     */
    private $loader;

    /**
     * @var ExtractorManager
     */
    private $extractor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FileWriter
     */
    private $writer;

    /**
     * @param LoaderManager                                     $loader
     * @param ExtractorManager                                  $extractor
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     * @param FileWriter                                        $writer
     */
    public function __construct(
        LoaderManager $loader,
        ExtractorManager $extractor,
        LoggerInterface $logger,
        FileWriter $writer
    ) {
        $this->loader = $loader;
        $this->extractor = $extractor;
        $this->logger = $logger;
        $this->writer = $writer;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->extractor->setLogger($logger);
    }

    /**
     * @param string $file
     * @param string $format
     * @param string $domain
     * @param string $locale
     * @param string $id
     */
    public function createTranslation($file, $format, $domain, $locale, $id)
    {
        /* @var $catalogue MessageCatalogue */
        $catalogue = $this->loader->loadFile($file, $format, $locale, $domain);
        $message = new Message($id, $domain);
        $catalogue->add($message);

        $this->writer->write($catalogue, $domain, $file, $format);
    }
}
