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
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Loader\LoaderInterface;
use JMS\TranslationBundle\Util\FileUtils;

class LoaderManager
{
    public function __construct(
        /** @var array<string, LoaderInterface> */
        private array $loaders,
    ) {
    }

    public function loadFile(mixed $file, string $format, string $locale, string $domain = 'messages'): MessageCatalogue
    {
        return $this->getLoader($format)->load($file, $locale, $domain);
    }

    public function loadFromDirectory(string $dir, string $targetLocale): MessageCatalogue
    {
        $files = FileUtils::findTranslationFiles($dir);

        $catalogue = new MessageCatalogue();
        $catalogue->setLocale($targetLocale);

        foreach ($files as $domain => $locales) {
            foreach ($locales as $locale => $data) {
                if ($locale !== $targetLocale) {
                    continue;
                }

                [$format, $file] = $data;

                $catalogue->merge($this->getLoader($format)->load($file, $locale, $domain));
            }
        }

        return $catalogue;
    }

    protected function getLoader(string $format): LoaderInterface
    {
        return $this->loaders[$format] ?? throw new InvalidArgumentException(sprintf('The format "%s" does not exist.', $format));
    }
}
