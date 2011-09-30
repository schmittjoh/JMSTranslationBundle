<?php

namespace JMS\TranslationBundle\Translation;

use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Util\FileUtils;

class LoaderManager
{
    private $loaders;

    public function __construct(array $loaders)
    {
        $this->loaders = $loaders;
    }

    public function loadFile($file, $format, $locale, $domain = 'messages')
    {
        return $this->getLoader($format)->load($file, $locale, $domain);
    }

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

                list($format, $file) = $data;
                $catalogue->merge($this->getLoader($format)
                    ->load($file, $locale, $domain));
            }
        }

        return $catalogue;
    }

    protected function getLoader($format)
    {
        if (!isset($this->loaders[$format])) {
            throw new \InvalidArgumentException(sprintf('The format "%s" does not exist.', $format));
        }

        return $this->loaders[$format];
    }
}