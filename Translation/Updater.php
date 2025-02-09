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
    public function __construct(
        private LoaderManager $loader,
        private ExtractorManager $extractor,
        private FileWriter $writer,
        private LoggerInterface $logger,
    ) {
    }

    public function setLogger(LoggerInterface $logger): static
    {
        $this->logger = $logger;
        $this->extractor->setLogger($logger);

        return $this;
    }

    public function getChangeSet(Config $config): ChangeSet
    {
        ['existingCatalogue' => $existingCatalogue, 'scannedCatalogue' => $scannedCatalogue] = $this->getCatalogues($config);

        $comparator = new CatalogueComparator();
        $comparator->setIgnoredDomains($config->getIgnoredDomains());
        $comparator->setDomains($config->getDomains());

        return $comparator->compare($existingCatalogue, $scannedCatalogue);
    }

    public function updateTranslation(string $file, string $format, string $domain, string $locale, string $id, string $trans): void
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
     */
    public function process(Config $config): void
    {
        ['scannedCatalogue' => $scannedCatalogue] = $this->getCatalogues($config);

        foreach ($scannedCatalogue->getDomains() as $name => $domain) {
            // skip domain not selected
            if ($config->hasDomains() && !$config->hasDomain($name)) {
                continue;
            }

            if ($config->isIgnoredDomain($name)) {
                continue;
            }

            $format = $this->detectOutputFormat($config, $name);

            // delete translation files of other formats
            $translationFileRegex = sprintf(
                '/^%s%s\.%s\.[^\.]+$/',
                $name,
                $config->shouldUseIcuMessageFormat() ? '+intl-icu' : '',
                $config->getLocale()
            );
            foreach (Finder::create()->name($translationFileRegex)->in($config->getTranslationsDir())->depth('< 1')->files() as $file) {
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
                $config->getTranslationsDir(),
                $name,
                $config->shouldUseIcuMessageFormat() ? '+intl-icu' : '',
                $config->getLocale(),
                $format
            );
            $this->logger->info(sprintf('Writing translation file "%s".', $outputFile));
            $this->writer->write($scannedCatalogue, $name, $outputFile, $format);
        }
    }

    /**
     * Detects the most suitable output format to use.
     */
    private function detectOutputFormat(Config $config, string $currentDomain): string
    {
        if (null !== $config->getOutputFormat()) {
            return $config->getOutputFormat();
        }

        // check if which translation files in which format exist
        $otherDomainFormat = $localeFormat = $otherLocaleFormat = null;
        foreach (FileUtils::findTranslationFiles($config->getTranslationsDir()) as $domain => $locales) {
            foreach ($locales as $locale => $fileData) {
                [$format] = $fileData;

                if ($currentDomain !== $domain) {
                    $otherDomainFormat = $format;
                    continue 2;
                }

                if ($config->getLocale() === $locale) {
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

        return $config->getDefaultOutputFormat();
    }

    /**
     * @return array{existingCatalogue: MessageCatalogue, scannedCatalogue: MessageCatalogue}
     */
    private function getCatalogues(Config $config): array
    {
        $this->logger->info(sprintf('Loading catalogues from "%s"', $config->getTranslationsDir()));
        $existingCatalogue = new MessageCatalogue();

        // load external resources, so current translations can be reused in the final translation
        foreach ($config->getLoadResources() as $resource) {
            $existingCatalogue->merge($this->loader->loadFromDirectory(
                $resource,
                $config->getLocale()
            ));
        }

        $existingCatalogue->merge($this->loader->loadFromDirectory(
            $config->getTranslationsDir(),
            $config->getLocale()
        ));

        $this->extractor->reset();
        $this->extractor->setDirectories($config->getScanDirs());
        $this->extractor->setExcludedDirs($config->getExcludedDirs());
        $this->extractor->setExcludedNames($config->getExcludedNames());
        $this->extractor->setEnabledExtractors($config->getEnabledExtractors());

        $this->logger->info('Extracting translation keys');
        $scannedCatalogue = $this->extractor->extract();
        $scannedCatalogue->setLocale($config->getLocale());

        // merge existing messages into scanned messages
        foreach ($scannedCatalogue->getDomains() as $domainCatalogue) {
            foreach ($domainCatalogue->all() as $message) {
                if (!$existingCatalogue->has($message)) {
                    continue;
                }

                $existingMessage = clone $existingCatalogue->get($message->getId(), $message->getDomain());
                $existingMessage->mergeScanned($message);
                $scannedCatalogue->set($existingMessage, true);
            }
        }

        if ($config->isKeepOldMessages()) {
            foreach ($existingCatalogue->getDomains() as $domainCatalogue) {
                foreach ($domainCatalogue->all() as $message) {
                    if ($scannedCatalogue->has($message)) {
                        continue;
                    }

                    $scannedCatalogue->add($message);
                }
            }
        }

        return ['existingCatalogue' => $existingCatalogue, 'scannedCatalogue' => $scannedCatalogue];
    }
}
