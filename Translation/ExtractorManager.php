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
use Symfony\Component\HttpKernel\Log\LoggerInterface;

use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileExtractor;

class ExtractorManager implements ExtractorInterface
{
    private $fileExtractor;
    private $customExtractors;
    private $directories = array();
    private $enabledExtractors = array();
    private $logger;

    public function __construct(FileExtractor $extractor, LoggerInterface $logger, array $customExtractors = array())
    {
        $this->fileExtractor = $extractor;
        $this->customExtractors = $customExtractors;
        $this->logger = $logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->fileExtractor->setLogger($logger);
    }

    public function setDirectories(array $directories)
    {
        foreach ($directories as $dir) {
            $this->addDirectory($dir);
        }
    }

    public function addDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $directory));
        }

        $this->directories[] = $directory;
    }

    public function setExcludedDirs(array $dirs)
    {
        $this->fileExtractor->setExcludedDirs($dirs);
    }

    public function setExcludedNames(array $names)
    {
        $this->fileExtractor->setExcludedNames($names);
    }

    public function setEnabledExtractors(array $aliases)
    {
        foreach ($aliases as $alias => $true) {
            if (!isset($this->customExtractors[$alias])) {
                throw new \InvalidArgumentException(sprintf('There is no extractor with alias "%s". Available extractors: %s', $alias, $this->customExtractors ? implode(', ', array_keys($this->customExtractors)) : '# none #'));
            }
        }

        $this->enabledExtractors = $aliases;
    }

    public function extract()
    {
        $catalogue = new MessageCatalogue();

        foreach ($this->directories as $directory) {
            $this->fileExtractor->setDirectory($directory);
            $catalogue->merge($this->fileExtractor->extract());
        }

        foreach ($this->customExtractors as $alias => $extractor) {
            if (!isset($this->enabledExtractors[$alias])) {
                $this->logger->debug(sprintf('Skipping custom extractor "%s" as it is not enabled.', $alias));
                continue;
            }

            $catalogue->merge($extractor->extract());
        }

        return $catalogue;
    }
}