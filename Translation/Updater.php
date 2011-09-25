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

use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Model\MessageCatalogue;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\Comparison\CatalogueComparator;
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
    private $updateRequest;
    private $existingCatalogue;
    private $scannedCatalogue;
    private $logger;
    private $writer;

    public function __construct(TranslationLoader $loader, ExtractorManager $extractor, LoggerInterface $logger, FileWriterInterface $writer)
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

    public function getChangeSet(UpdateRequest $request)
    {
        $this->setUpdateRequest($request);

        $comparator = new CatalogueComparator();
        $comparator->setIgnoredDomains($this->updateRequest->getIgnoredDomains());

        return $comparator->compare($this->existingCatalogue, $this->scannedCatalogue);
    }

    /**
     * This writes any updates to the disk.
     *
     * This will not change files of ignored domains. It will also not
     * change files of another than the current locale.
     *
     * @return void
     */
    public function process(UpdateRequest $request)
    {
        $this->setUpdateRequest($request);

        $cataloguePerDomain = array();
        foreach ($this->scannedCatalogue->all() as $id => $message) {
            if ($this->updateRequest->isIgnoredDomain($domain = $message->getDomain())) {
                continue;
            }

            if (isset($cataloguePerDomain[$domain])) {
                continue;
            }

            $cataloguePerDomain[$domain] = clone $this->scannedCatalogue;
            $cataloguePerDomain[$domain]->filter(function($v) use ($domain) { return $domain === $v->getDomain(); });
        }

        foreach ($cataloguePerDomain as $domain => $catalogue) {
            $format = $this->detectOutputFormat($domain);

            // delete translation files of other formats
            foreach (Finder::create()->name('/^'.$domain.'\.'.$this->updateRequest->getLocale().'\.[^\.]+$/')->in($this->updateRequest->getTranslationsDir())->depth('< 1')->files() as $file) {
                if ('.'.$format === substr($file, -1 * strlen('.'.$format))) {
                    continue;
                }

                $this->logger->info(sprintf('Deleting translation file "%s".', $file));

                if (false === @unlink($file)) {
                    throw new RuntimeException(sprintf('Could not delete the translation file "%s".', $file));
                }
            }

            $outputFile = $this->updateRequest->getTranslationsDir().'/'.$domain.'.'.$this->updateRequest->getLocale().'.'.$format;
            $this->logger->info(sprintf('Writing translation file "%s".', $outputFile));
            $this->writer->write($catalogue, $outputFile, $format);
        }
    }

    /**
     * Detects the most suitable output format to use.
     *
     * @param string $domain
     * @throws \RuntimeException
     * @return string the output format
     */
    private function detectOutputFormat($domain)
    {
        if (null !== $this->updateRequest->getOutputFormat()) {
            return $this->updateRequest->getOutputFormat();
        }

        // check if which translation files in which format exist
        $otherDomainFormat = $localeFormat = $otherLocaleFormat = null;
        foreach (Finder::create()->in($this->updateRequest->getTranslationsDir())->depth('< 1')->files() as $file) {
            if (!preg_match('/^([^\.]+)\.([^\.]+)\.([^\.]+)$/', basename($file), $match)) {
                continue;
            }

            if ($domain !== $match[1]) {
                $otherDomainFormat = $match[3];
                continue;
            }

            if ($this->updateRequest->getLocale() === $match[2]) {
                $localeFormat = $match[3];
                continue;
            }

            $otherLocaleFormat = $match[3];
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

        return $this->updateRequest->getDefaultOutputFormat();
    }

    private function setUpdateRequest(UpdateRequest $request)
    {
        if (null === $request->getTranslationsDir()) {
            throw new RuntimeException('The translations directory must be set.');
        }
        if (null === $request->getLocale()) {
            throw new RuntimeException('The locale must be set.');
        }

        $this->updateRequest = $request;
        $this->updateExistingCatalogue();
        $this->updateScannedCatalogue();
    }

    private function updateScannedCatalogue()
    {
        $this->extractor->setDirectories($this->updateRequest->getScanDirs());
        $this->extractor->setExcludedDirs($this->updateRequest->getExcludedDirs());
        $this->extractor->setExcludedNames($this->updateRequest->getExcludedNames());
        $this->extractor->setEnabledExtractors($this->updateRequest->getEnabledExtractors());

        $this->scannedCatalogue = $this->extractor->extract();
        $this->scannedCatalogue->setLocale($this->updateRequest->getLocale());

        // set translations where already available
        foreach ($this->scannedCatalogue->all() as $id => $message) {
            if (!$this->existingCatalogue->has($id, $message->getDomain())) {
                continue;
            }

            $message->setLocaleString($this->existingCatalogue->get($id, $message->getDomain()));
            $message->setNew(false);
        }
    }

    private function updateExistingCatalogue()
    {
        $this->existingCatalogue = new SymfonyMessageCatalogue($this->updateRequest->getLocale());
        $this->loader->loadMessages($this->updateRequest->getTranslationsDir(), $this->existingCatalogue);
    }
}