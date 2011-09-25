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

/**
 * UpdateRequest.
 *
 * This class contains all configuration for the Updater.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class UpdateRequest
{
    private $translationsDir;
    private $ignoredDomains = array();
    private $outputFormat;
    private $defaultOutputFormat = 'yml';
    private $locale;
    private $scanDirs;
    private $excludedDirs = array('Tests');
    private $excludedNames = array('*Test.php', '*TestCase.php');
    private $enabledExtractors = array();

    /**
    * Sets the default output format.
    *
    * The default output format is used when the following conditions are met:
    *   - there is no existing file for the given domain
    *   - you haven't forced a format
    *
    * @param string $format
    */
    public function setDefaultOutputFormat($format)
    {
        $this->defaultOutputFormat = $format;
    }

    /**
     * Sets the output format.
     *
     * This will force all updated domains to be in this format even if input
     * files have a different format. This will also cause input files of
     * another format to be deleted.
     *
     * @param string $format
     */
    public function setOutputFormat($format)
    {
        $this->outputFormat = $format;
    }

    /**
     * Sets ignored domains.
     *
     * These domains are not altered by the update() command, and also do not
     * appear in the change set calculated by getChangeSet().
     *
     * @param array $domains an array of the form array('domain' => true, 'another_domain' => true)
     * @return void
     */
    public function setIgnoredDomains(array $domains)
    {
        $this->ignoredDomains = $domains;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function setTranslationsDir($dir)
    {
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true)) {
                throw new RuntimeException(sprintf('The translations directory "%s" could not be created.'));
            }
        }

        $this->translationsDir = rtrim($dir, '\\/');
    }

    public function setScanDirs(array $dirs)
    {
        foreach ($dirs as $k => $dir) {
            if (!is_dir($dir)) {
                throw new RuntimeException(sprintf('The scan directory "%s" does not exist.', $dir));
            }

            $dirs[$k] = rtrim($dir, '\\/');
        }

        $this->scanDirs = $dirs;
    }

    public function setExcludedDirs(array $dirs)
    {
        $this->excludedDirs = $dirs;
    }

    public function setExcludedNames(array $names)
    {
        $this->excludedNames = $names;
    }

    public function setEnabledExtractors(array $aliases)
    {
        $this->enabledExtractors = $aliases;
    }

    public function getTranslationsDir()
    {
        return $this->translationsDir;
    }

    public function isIgnoredDomain($domain)
    {
        return isset($this->ignoredDomains[$domain]);
    }

    public function getIgnoredDomains()
    {
        return $this->ignoredDomains;
    }

    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    public function getDefaultOutputFormat()
    {
        return $this->defaultOutputFormat;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getScanDirs()
    {
        return $this->scanDirs;
    }

    public function getExcludedDirs()
    {
        return $this->excludedDirs;
    }

    public function getExcludedNames()
    {
        return $this->excludedNames;
    }

    public function getEnabledExtractors()
    {
        return $this->enabledExtractors;
    }
}