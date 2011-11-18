<?php

namespace JMS\TranslationBundle\Translation;

use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Util\FileUtils;
use JMS\TranslationBundle\Translation\Loader\LoaderInterface;

class LoaderManager
{
    private $loaders;

    /**
     * @param array $loaders
     */
    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * @param $file
     * @param $format
     * @param $locale
     * @param string $domain
     * @return mixed
     */
    public function loadFile($file, $format, $locale, $domain = 'messages')
    {
        return $this->getLoader($format)->load($file, $locale, $domain);
    }

    /**
     * @param $dir
     * @param $targetLocale
     * @return \JMS\TranslationBundle\Model\MessageCatalogue
     */
    public function loadFromDirectory($dir, $targetLocale)
    {
        $files = FileUtils::findTranslationFiles($dir);

        $catalogue = new MessageCatalogue();
        $catalogue->setLocale($targetLocale);

        foreach ($files as $domain => $locales) {
            foreach ($locales as $locale => $data) {
                if ($locale !== $targetLocale) {
                    continue;
                }

                $domain = $catalogue->getOrCreateDomain($domain);

                list($format, $file) = $data;

                $domain->merge($this->getLoader($format)->load($file, $locale, $domain->getName()));

            }
        }

        return $catalogue;
    }

    /**
     * @param $format
     * @return mixed
     * @throws \InvalidArgumentException
     * @return \JMS\TranslationBundle\Translation\Loader\LoaderInterface
     */
    protected function getLoader($format)
    {
        if (!isset($this->loaders[$format])) {
            throw new \InvalidArgumentException(sprintf('The format "%s" does not exist.', $format));
        }

        return $this->loaders[$format];
    }
}