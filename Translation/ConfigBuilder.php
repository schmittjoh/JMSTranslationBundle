<?php

namespace JMS\TranslationBundle\Translation;

final class ConfigBuilder
{
    private $translationsDir;
    private $locale;
    private $ignoredDomains = array();
    private $outputFormat;
    private $defaultOutputFormat = 'yml';
    private $scanDirs;
    private $excludedDirs = array('Tests');
    private $excludedNames = array('*Test.php', '*TestCase.php');
    private $enabledExtractors = array();

    public static function fromConfig(Config $config)
    {
        $builder = new self();
        $builder->setTranslationsDir($config->getTranslationsDir());
        $builder->setLocale($config->getLocale());
        $builder->setIgnoredDomains($config->getIgnoredDomains());
        $builder->setOutputFormat($config->getOutputFormat());
        $builder->setDefaultOutputFormat($config->getDefaultOutputFormat());
        $builder->setScanDirs($config->getScanDirs());
        $builder->setExcludedDirs($config->getExcludedDirs());
        $builder->setExcludedNames($config->getExcludedNames());
        $builder->setEnabledExtractors($config->getEnabledExtractors());

        return $builder;
    }

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

        return $this;
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

        return $this;
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

        return $this;
    }

    public function addIgnoredDomain($domain)
    {
        $this->ignoredDomains[$domain] = true;

        return $this;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function setTranslationsDir($dir)
    {
        $this->translationsDir = $dir;

        return $this;
    }

    public function setScanDirs(array $dirs)
    {
        $this->scanDirs = $dirs;

        return $this;
    }

    public function setExcludedDirs(array $dirs)
    {
        $this->excludedDirs = $dirs;

        return $this;
    }

    public function setExcludedNames(array $names)
    {
        $this->excludedNames = $names;

        return $this;
    }

    public function setEnabledExtractors(array $aliases)
    {
        $this->enabledExtractors = $aliases;

        return $this;
    }

    public function enableExtractor($alias)
    {
        $this->enabledExtractors[$alias] = true;

        return $this;
    }

    public function disableExtractor($alias)
    {
        unset($this->enabledExtractors[$alias]);

        return $this;
    }

    public function getConfig()
    {
        return new Config(
            $this->translationsDir,
            $this->locale,
            $this->ignoredDomains,
            $this->outputFormat,
            $this->defaultOutputFormat,
            $this->scanDirs,
            $this->excludedDirs,
            $this->excludedNames,
            $this->enabledExtractors
        );
    }
}