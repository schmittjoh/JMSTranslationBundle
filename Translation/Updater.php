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

namespace JMS\TranslationBundle\Translation;

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
 * calculating changes in the message catalogue, and persisting updates
 * to them.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Updater
{
    private $loader;
    private $extractor;

    /**
     * @var Config
     */
    private $config;
    private $existingCatalogue;
    private $scannedCatalogue;
    private $logger;
    private $writer;

    public function __construct(LoaderManager $loader, ExtractorManager $extractor, LoggerInterface $logger, FileWriter $writer)
    {
        $this->loader = $loader;
        $this->extractor = $extractor;
        $this->logger = $logger;
        $this->writer = $writer;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->extractor->setLogger($logger);
    }

    public function getChangeSet(Config $config)
    {
        $this->setConfig($config);

        $comparator = new CatalogueComparator();
        $comparator->setIgnoredDomains($this->config->getIgnoredDomains());

        return $comparator->compare($this->existingCatalogue, $this->scannedCatalogue);
    }

    public function updateTranslation($file, $format, $domain, $locale, $id, $trans)
    {
        $catalogue = $this->loader->loadFile($file, $format, $locale, $domain);
        $catalogue
            ->get($id)
            ->setLocaleString($trans)
            ->setNew(false)
        ;

        $this->writer->write($catalogue, $file, $format);
    }

    /**
     * This writes any updates to the disk.
     *
     * This will not change files of ignored domains. It will also not
     * change files of another than the current locale.
     *
     * @return void
     */
    public function process(Config $config)
    {
        $this->setConfig($config);

        $cataloguePerDomain = array();
        foreach ($this->scannedCatalogue->all() as $domain) {
            // skip domain not selected
            if ($this->config->hasDomains() && !$this->config->hasDomain($domain->getName())) {
                continue;
            }

            if ($this->config->isIgnoredDomain($domain->getName())) {
                continue;
            }

            if (isset($cataloguePerDomain[$domain->getName()])) {
                continue;
            }

            $cataloguePerDomain[$domain->getName()] = clone $domain;
            $cataloguePerDomain[$domain->getName()]->filter(function($message) use ($domain) { return $domain->getName() === $message->getDomain(); });
        }

        foreach ($cataloguePerDomain as $domain) {
            $format = $this->detectOutputFormat($domain->getName());

            // delete translation files of other formats
            foreach (Finder::create()->name('/^'.$domain->getName().'\.'.$this->config->getLocale().'\.[^\.]+$/')->in($this->config->getTranslationsDir())->depth('< 1')->files() as $file) {
                if ('.'.$format === substr($file, -1 * strlen('.'.$format))) {
                    continue;
                }

                $this->logger->info(sprintf('Deleting translation file "%s".', $file));

                if (false === @unlink($file)) {
                    throw new RuntimeException(sprintf('Could not delete the translation file "%s".', $file));
                }
            }

            $outputFile = $this->config->getTranslationsDir().'/'.$domain->getName().'.'.$this->config->getLocale().'.'.$format;
            $this->logger->info(sprintf('Writing translation file "%s".', $outputFile));
            $this->writer->write($domain, $outputFile, $format);
        }
    }

    /**
     * Detects the most suitable output format to use.
     *
     * @param string $domain
     * @throws \RuntimeException
     * @return string the output format
     */
    private function detectOutputFormat($currentDomain)
    {
        if (null !== $this->config->getOutputFormat()) {
            return $this->config->getOutputFormat();
        }

        // check if which translation files in which format exist
        $otherDomainFormat = $localeFormat = $otherLocaleFormat = null;
        foreach (FileUtils::findTranslationFiles($this->config->getTranslationsDir()) as $domain => $locales) {
            foreach ($locales as $locale => $fileData) {
                list($format, ) = $fileData;

                if ($currentDomain !== $domain) {
                    $otherDomainFormat = $format;
                    continue 2;
                }

                if ($this->config->getLocale() === $locale) {
                    $localeFormat = $format;
                    continue;
                }

                $otherLocaleFormat = $format;
            }
        }

        if (null !== $localeFormat) {
            return $localeFormat;
        }

        if (null !== $otherLocaleFormat) {
            return $otherLocaleFormat;
        }

        if (null !== $otherDomainFormat) {
            return $otherDomainFormat;
        }

        return $this->config->getDefaultOutputFormat();
    }

    private function setConfig(Config $config)
    {
        $this->config = $config;

        $this->existingCatalogue = $this->loader->loadFromDirectory(
            $config->getTranslationsDir(), $config->getLocale()
        );

        $this->extractor->setDirectories($config->getScanDirs());
        $this->extractor->setExcludedDirs($config->getExcludedDirs());
        $this->extractor->setExcludedNames($config->getExcludedNames());
        $this->extractor->setEnabledExtractors($config->getEnabledExtractors());

        $this->scannedCatalogue = $this->extractor->extract();
        $this->scannedCatalogue->setLocale($config->getLocale());

        // merge existing messages into scanned messages
        foreach ($this->scannedCatalogue->all() as $domain) {
            foreach ($domain->all() as $message) {
                if (!$this->existingCatalogue->has($message)) {
                    continue;
                }

                $message->mergeExisting($this->existingCatalogue->get($message->getId(), $message->getDomain()));
            }
        }
    }
}