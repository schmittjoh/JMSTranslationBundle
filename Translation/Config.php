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
    private string $translationsDir;

    private string $locale;

    private array $ignoredDomains;

    private array $domains;

    private string|null $outputFormat = null;

    private string $defaultOutputFormat;

    private bool $useIcuMessageFormat;

    private array $scanDirs;

    private array $excludedDirs;

    private array $excludedNames;

    private array $enabledExtractors;

    private bool $keepOldMessages;

    private array $loadResources;

    public function __construct(string $translationsDir, string $locale, array $ignoredDomains, array $domains, string|null $outputFormat, string $defaultOutputFormat, bool $useIcuMessageFormat, array $scanDirs, array $excludedDirs, array $excludedNames, array $enabledExtractors, bool $keepOldMessages, array $loadResources)
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
        $this->useIcuMessageFormat = $useIcuMessageFormat;
        $this->locale = $locale;
        $this->scanDirs = $scanDirs;
        $this->excludedDirs = $excludedDirs;
        $this->excludedNames = $excludedNames;
        $this->enabledExtractors = $enabledExtractors;
        $this->keepOldMessages = $keepOldMessages;
        $this->loadResources = $loadResources;
    }

    public function getTranslationsDir(): string
    {
        return $this->translationsDir;
    }

    public function isIgnoredDomain(string $domain): bool
    {
        return isset($this->ignoredDomains[$domain]);
    }

    public function getIgnoredDomains(): array
    {
        return $this->ignoredDomains;
    }

    public function hasDomain(string $domain): bool
    {
        return isset($this->domains[$domain]);
    }

    public function hasDomains(): bool
    {
        return count($this->domains) > 0;
    }

    public function getDomains(): array
    {
        return $this->domains;
    }

    public function getOutputFormat(): string|null
    {
        return $this->outputFormat;
    }

    public function getDefaultOutputFormat(): string
    {
        return $this->defaultOutputFormat;
    }

    public function shouldUseIcuMessageFormat(): bool
    {
        return $this->useIcuMessageFormat;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getScanDirs(): array
    {
        return $this->scanDirs;
    }

    public function getExcludedDirs(): array
    {
        return $this->excludedDirs;
    }

    public function getExcludedNames(): array
    {
        return $this->excludedNames;
    }

    public function getEnabledExtractors(): array
    {
        return $this->enabledExtractors;
    }

    public function isKeepOldMessages(): bool
    {
        return $this->keepOldMessages;
    }

    public function getLoadResources(): array
    {
        return $this->loadResources;
    }
}
