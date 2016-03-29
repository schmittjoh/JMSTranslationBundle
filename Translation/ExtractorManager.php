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

use JMS\TranslationBundle\Exception\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileExtractor;
use JMS\TranslationBundle\Logger\LoggerAwareInterface;

class ExtractorManager implements ExtractorInterface
{
    private $fileExtractor;
    private $customExtractors;
    private $directories = array();
    private $enabledExtractors = array();
    private $logger;

    /**
     * @param Extractor\FileExtractor $extractor
     * @param LoggerInterface $logger
     * @param array $customExtractors
     */
    public function __construct(FileExtractor $extractor, LoggerInterface $logger, array $customExtractors = array())
    {
        $this->fileExtractor = $extractor;
        $this->customExtractors = $customExtractors;
        $this->logger = $logger;
    }

    public function reset()
    {
        $this->directories       = array();
        $this->enabledExtractors = array();
        $this->fileExtractor->reset();
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->fileExtractor->setLogger($logger);

        foreach ($this->customExtractors as $extractor) {
            if (!$extractor instanceof LoggerAwareInterface) {
                continue;
            }

            $extractor->setLogger($logger);
        }
    }

    /**
     * @param array $directories
     */
    public function setDirectories(array $directories)
    {
        $this->directories = array();
        
        foreach ($directories as $dir) {
            $this->addDirectory($dir);
        }
    }

    /**
     * @param $directory
     * @throws \JMS\TranslationBundle\Exception\InvalidArgumentException
     */
    public function addDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $directory));
        }

        $this->directories[] = $directory;
    }

    /**
     * @param array $dirs
     */
    public function setExcludedDirs(array $dirs)
    {
        $this->fileExtractor->setExcludedDirs($dirs);
    }

    /**
     * @param array $names
     */
    public function setExcludedNames(array $names)
    {
        $this->fileExtractor->setExcludedNames($names);
    }

    /**
     * @param array $aliases
     * @throws \JMS\TranslationBundle\Exception\InvalidArgumentException
     */
    public function setEnabledExtractors(array $aliases)
    {
        foreach ($aliases as $alias => $true) {
            if (!isset($this->customExtractors[$alias])) {
                throw new InvalidArgumentException(sprintf('There is no extractor with alias "%s". Available extractors: %s', $alias, $this->customExtractors ? implode(', ', array_keys($this->customExtractors)) : '# none #'));
            }
        }

        $this->enabledExtractors = $aliases;
    }

    /**
     * @return \JMS\TranslationBundle\Model\MessageCatalogue
     */
    public function extract()
    {
        $catalogue = new MessageCatalogue();

        foreach ($this->directories as $directory) {
            $this->logger->info(sprintf('Extracting messages from directory : %s', $directory));
            $this->fileExtractor->setDirectory($directory);
            $catalogue->merge($this->fileExtractor->extract());
        }

        foreach ($this->customExtractors as $alias => $extractor) {
            if (!isset($this->enabledExtractors[$alias])) {
                $this->logger->debug(sprintf('Skipping custom extractor "%s" as it is not enabled.', $alias));
                continue;
            }

            $this->logger->info(sprintf('Extracting messages with custom extractor : %s', $alias));

            $catalogue->merge($extractor->extract());
        }

        return $catalogue;
    }
}
