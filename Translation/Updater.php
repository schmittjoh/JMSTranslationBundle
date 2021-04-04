<?php

declare(strict_types=1);

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

use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Comparison\CatalogueComparator;
use JMS\TranslationBundle\Translation\Comparison\ChangeSet;
use JMS\TranslationBundle\Util\FileUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;

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
    /**
     * @var LoaderManager
     */
    private $loader;

    /**
     * @var ExtractorManager
     */
    private $extractor;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var MessageCatalogue
     */
    private $existingCatalogue;

    /**
     * @var MessageCatalogue
     */
    private $scannedCatalogue;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FileWriter
     */
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

    /**
     * @param Config $config
     *
     * @return ChangeSet
     */
    public function getChangeSet(Config $config)
    {
        $this->setConfig($config);

        $comparator = new CatalogueComparator();
        $comparator->setIgnoredDomains($this->config->getIgnoredDomains());
        $comparator->setDomains($this->config->getDomains());

        return $comparator->compare($this->existingCatalogue, $this->scannedCatalogue);
    }

    /**
     * @param string $file
     * @param string $format
     * @param string $domain
     * @param string $locale
     * @param string $id
     * @param string $trans
     */
    public function updateTranslation($file, $format, $domain, $locale, $id, $trans)
    {
        $catalogue = $this->loader->loadFile($file, $format, $locale, $domain);
        $catalogue
            ->get($id, $domain)
            ->setLocaleString($trans)
            ->setNew(false);

        $this->writer->write($catalogue, $domain, $file, $format);
    }

    /**
     * This writes any updates to the disk.
     *
     * This will not change files of ignored domains. It will also not
     * change files of another than the current locale.
     *
     * @param Config $config
     */
    public function process(Config $config)
    {
        $this->setConfig($config);

        foreach ($this->scannedCatalogue->getDomains() as $name => $domain) {
            // skip domain not selected
            if ($this->config->hasDomains() && !$this->config->hasDomain($name)) {
                continue;
            }

            if ($this->config->isIgnoredDomain($name)) {
                continue;
            }

            $format = $this->detectOutputFormat($name);

            // delete translation files of other formats
            $translationFileRegex = sprintf(
                '/^%s%s\.%s\.[^\.]+$/',
                $name,
                $this->config->shouldUseIcuMessageFormat() ? '+intl-icu' : '',
                $this->config->getLocale()
            );
            foreach (Finder::create()->name($translationFileRegex)->in($this->config->getTranslationsDir())->depth('< 1')->files() as $file) {
                if ('.' . $format === substr((string) $file, -1 * strlen('.' . $format))) {
                    continue;
                }

                $this->logger->info(sprintf('Deleting translation file "%s".', $file));

                if (false === @unlink((string) $file)) {
                    throw new RuntimeException(sprintf('Could not delete the translation file "%s".', $file));
                }
            }

            $outputFile = sprintf(
                '%s/%s%s.%s.%s',
                $this->config->getTranslationsDir(),
                $name,
                $this->config->shouldUseIcuMessageFormat() ? '+intl-icu' : '',
                $this->config->getLocale(),
                $format
            );
            $this->logger->info(sprintf('Writing translation file "%s".', $outputFile));
            $this->writer->write($this->scannedCatalogue, $name, $outputFile, $format);
        }
    }

    /**
     * Detects the most suitable output format to use.
     *
     * @internal param string $domain
     *
     * @param string $currentDomain
     *
     * @return string
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
                [$format] = $fileData;

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

        $this->logger->info(sprintf('Loading catalogues from "%s"', $config->getTranslationsDir()));
        $this->existingCatalogue = new MessageCatalogue();

        // load external resources, so current translations can be reused in the final translation
        foreach ($config->getLoadResources() as $resource) {
            $this->existingCatalogue->merge($this->loader->loadFromDirectory(
                $resource,
                $config->getLocale()
            ));
        }

        $this->existingCatalogue->merge($this->loader->loadFromDirectory(
            $config->getTranslationsDir(),
            $config->getLocale()
        ));

        $this->extractor->reset();
        $this->extractor->setDirectories($config->getScanDirs());
        $this->extractor->setExcludedDirs($config->getExcludedDirs());
        $this->extractor->setExcludedNames($config->getExcludedNames());
        $this->extractor->setEnabledExtractors($config->getEnabledExtractors());

        $this->logger->info('Extracting translation keys');
        $this->scannedCatalogue = $this->extractor->extract();
        $this->scannedCatalogue->setLocale($config->getLocale());

        // merge existing messages into scanned messages
        foreach ($this->scannedCatalogue->getDomains() as $domainCatalogue) {
            foreach ($domainCatalogue->all() as $message) {
                if (!$this->existingCatalogue->has($message)) {
                    continue;
                }

                $existingMessage = clone $this->existingCatalogue->get($message->getId(), $message->getDomain());
                $existingMessage->mergeScanned($message);
                $this->scannedCatalogue->set($existingMessage, true);
            }
        }

        if ($this->config->isKeepOldMessages()) {
            foreach ($this->existingCatalogue->getDomains() as $domainCatalogue) {
                foreach ($domainCatalogue->all() as $message) {
                    if ($this->scannedCatalogue->has($message)) {
                        continue;
                    }

                    $this->scannedCatalogue->add($message);
                }
            }
        }
    }
}
