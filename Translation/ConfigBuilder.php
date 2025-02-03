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

final class ConfigBuilder
{
    private string|null $translationsDir = null;

    private string|null $locale = null;

    private array $ignoredDomains = [];

    private array $domains = [];

    private string|null $outputFormat = null;

    private string $defaultOutputFormat = 'xlf';

    private bool $useIcuMessageFormat = false;

    private array $scanDirs = [];

    private array $excludedDirs = ['Tests'];

    private array $excludedNames = ['*Test.php', '*TestCase.php'];

    private array $enabledExtractors = [];

    private bool $keepOldTranslations = false;

    private array $loadResources = [];

    public static function fromConfig(Config $config): ConfigBuilder
    {
        $builder = new self();
        $builder->setTranslationsDir($config->getTranslationsDir());
        $builder->setLocale($config->getLocale());
        $builder->setIgnoredDomains($config->getIgnoredDomains());
        $builder->setDomains($config->getDomains());
        $builder->setOutputFormat($config->getOutputFormat());
        $builder->setDefaultOutputFormat($config->getDefaultOutputFormat());
        $builder->setUseIcuMessageFormat($config->shouldUseIcuMessageFormat());
        $builder->setScanDirs($config->getScanDirs());
        $builder->setExcludedDirs($config->getExcludedDirs());
        $builder->setExcludedNames($config->getExcludedNames());
        $builder->setEnabledExtractors($config->getEnabledExtractors());
        $builder->setLoadResources($config->getLoadResources());

        return $builder;
    }

    /**
     * Sets the default output format.
     *
     * The default output format is used when the following conditions are met:
     *   - there is no existing file for the given domain
     *   - you haven't forced a format
     */
    public function setDefaultOutputFormat(string $format): self
    {
        $this->defaultOutputFormat = $format;

        return $this;
    }

    /**
     * Sets the output format.
     *
     * This will force all updated domains to be in this format even if input
     * files have a different format. This will also cause input files of
     * another format to be deleted.
     */
    public function setOutputFormat(string $format): self
    {
        $this->outputFormat = $format;

        return $this;
    }

    /**
     * Defines whether the ICU message format should be used.
     *
     * If enabled, translation files will be suffixed with +intl-icu, e.g.:
     * message+intl-icu.en.xlf
     */
    public function setUseIcuMessageFormat(bool $useIcuMessageFormat): self
    {
        $this->useIcuMessageFormat = $useIcuMessageFormat;

        return $this;
    }

    /**
     * Sets ignored domains.
     *
     * These domains are not altered by the update() command, and also do not
     * appear in the change set calculated by getChangeSet().
     *
     * @param array $domains an array of the form array('domain' => true, 'another_domain' => true)
     */
    public function setIgnoredDomains(array $domains): self
    {
        $this->ignoredDomains = $domains;

        return $this;
    }

    public function addIgnoredDomain(string $domain): self
    {
        $this->ignoredDomains[$domain] = true;

        return $this;
    }

    public function setDomains(array $domains): self
    {
        $this->domains = $domains;

        return $this;
    }

    public function addDomain(string $domain): self
    {
        $this->domains[$domain] = true;

        return $this;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function setTranslationsDir(string $dir): self
    {
        $this->translationsDir = $dir;

        return $this;
    }

    public function setScanDirs(array $dirs): self
    {
        $this->scanDirs = $dirs;

        return $this;
    }

    public function setExcludedDirs(array $dirs): self
    {
        $this->excludedDirs = $dirs;

        return $this;
    }

    public function setExcludedNames(array $names): self
    {
        $this->excludedNames = $names;

        return $this;
    }

    public function setEnabledExtractors(array $aliases): self
    {
        $this->enabledExtractors = $aliases;

        return $this;
    }

    public function enableExtractor(string $alias): self
    {
        $this->enabledExtractors[$alias] = true;

        return $this;
    }

    public function disableExtractor(string $alias): self
    {
        unset($this->enabledExtractors[$alias]);

        return $this;
    }

    public function setKeepOldTranslations(bool $value): self
    {
        $this->keepOldTranslations = $value;

        return $this;
    }

    public function getConfig(): Config
    {
        return new Config(
            $this->translationsDir,
            $this->locale,
            $this->ignoredDomains,
            $this->domains,
            $this->outputFormat,
            $this->defaultOutputFormat,
            $this->useIcuMessageFormat,
            $this->scanDirs,
            $this->excludedDirs,
            $this->excludedNames,
            $this->enabledExtractors,
            $this->keepOldTranslations,
            $this->loadResources
        );
    }

    public function setLoadResources(array $loadResources): self
    {
        $this->loadResources = $loadResources;

        return $this;
    }
}
