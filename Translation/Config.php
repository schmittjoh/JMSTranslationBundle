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
use JMS\TranslationBundle\Exception\RuntimeException;

/**
 * Configuration.
 *
 * This class contains all configuration for the Updater.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class Config
{
    /**
     * @var string
     */
    private $translationsDir;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var array
     */
    private $ignoredDomains;

    /**
     * @var array
     */
    private $domains;

    /**
     * @var string
     */
    private $outputFormat;

    /**
     * @var string
     */
    private $defaultOutputFormat;

    /**
     * @var array
     */
    private $scanDirs;

    /**
     * @var array
     */
    private $excludedDirs;

    /**
     * @var array
     */
    private $excludedNames;

    /**
     * @var array
     */
    private $enabledExtractors;

    /**
     * @var bool
     */
    private $keepOldMessages;

    /**
     * @var bool
     */
    private $keepOldTranslationMessages;

    /**
     * @var array
     */
    private $loadResources;

    /**
     * Config constructor.
     * @param $translationsDir
     * @param $locale
     * @param array $ignoredDomains
     * @param array $domains
     * @param $outputFormat
     * @param $defaultOutputFormat
     * @param array $scanDirs
     * @param array $excludedDirs
     * @param array $excludedNames
     * @param array $enabledExtractors
     * @param bool $keepOldMessages
     * @param bool $keepOldTranslationMessages
     * @param array $loadResources
     */
    public function __construct($translationsDir, $locale, array $ignoredDomains, array $domains, $outputFormat, $defaultOutputFormat, array $scanDirs, array $excludedDirs, array $excludedNames, array $enabledExtractors, $keepOldMessages, $keepOldTranslationMessages, array $loadResources)
    {
        if (empty($translationsDir)) {
            throw new InvalidArgumentException('The directory where translations are must be set.');
        }

        if (!is_dir($translationsDir)) {
            if (false === @mkdir($translationsDir, 0777, true)) {
                throw new RuntimeException(sprintf('The translations directory "%s" could not be created.', $translationsDir));
            }
        }

        if (empty($scanDirs)) {
            throw new InvalidArgumentException('You must pass at least one directory which should be scanned.');
        }

        foreach ($scanDirs as $k => $dir) {
            if (!is_dir($dir)) {
                throw new RuntimeException(sprintf('The scan directory "%s" does not exist.', $dir));
            }

            $scanDirs[$k] = rtrim($dir, '\\/');
        }

        if (empty($locale)) {
            throw new InvalidArgumentException('The locale cannot be empty.');
        }

        $this->translationsDir = rtrim($translationsDir, '\\/');
        $this->ignoredDomains = $ignoredDomains;
        $this->domains = $domains;
        $this->outputFormat = $outputFormat;
        $this->defaultOutputFormat = $defaultOutputFormat;
        $this->locale = $locale;
        $this->scanDirs = $scanDirs;
        $this->excludedDirs = $excludedDirs;
        $this->excludedNames = $excludedNames;
        $this->enabledExtractors = $enabledExtractors;
        $this->keepOldMessages = $keepOldMessages;
        $this->keepOldTranslationMessages = $keepOldTranslationMessages;
        $this->loadResources = $loadResources;
    }

    /**
     * @return string
     */
    public function getTranslationsDir()
    {
        return $this->translationsDir;
    }

    /**
     * @param $domain
     * @return bool
     */
    public function isIgnoredDomain($domain)
    {
        return isset($this->ignoredDomains[$domain]);
    }

    /**
     * @return array
     */
    public function getIgnoredDomains()
    {
        return $this->ignoredDomains;
    }

    /**
     * @param $domain
     * @return bool
     */
    public function hasDomain($domain)
    {
        return isset($this->domains[$domain]);
    }

    /**
     * @return bool
     */
    public function hasDomains()
    {
        return count($this->domains) > 0;
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * @return string
     */
    public function getDefaultOutputFormat()
    {
        return $this->defaultOutputFormat;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return array
     */
    public function getScanDirs()
    {
        return $this->scanDirs;
    }

    /**
     * @return array
     */
    public function getExcludedDirs()
    {
        return $this->excludedDirs;
    }

    /**
     * @return array
     */
    public function getExcludedNames()
    {
        return $this->excludedNames;
    }

    /**
     * @return array
     */
    public function getEnabledExtractors()
    {
        return $this->enabledExtractors;
    }

    /**
     * @return bool
     */
    public function isKeepOldMessages()
    {
        return $this->keepOldMessages;
    }

    /**
     * @return bool
     */
    public function isKeepOldTranslationMessages()
    {
        return $this->keepOldTranslationMessages;
    }

    /**
     * @return array
     */
    public function getLoadResources()
    {
        return $this->loadResources;
    }
}
