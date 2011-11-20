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

namespace JMS\TranslationBundle\Translation\Loader;

use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Model\Message;
use Symfony\Component\Translation\Loader\LoaderInterface as SymfonyLoader;

/**
 * Adapter for Symfony's own loaders.
 *
 * Using these loaders comes at the cost of loosing valuable information.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class SymfonyLoaderAdapter implements LoaderInterface
{
    private $loader;

    public function __construct(SymfonyLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Converts Symfony's message catalogue to the catalogue of this
     * bundle.
     *
     * @param mixed $resource
     * @param string $locale
     * @param string $domain
     * @return MessageCatalogue
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $catalogue = new MessageCatalogue();
        $catalogue->setLocale($locale);

        foreach ($this->loader->load($resource, $locale, $domain)->all($domain) as $id => $message) {
            $catalogue->add(
                Message::create($id, $domain)
                    ->setLocaleString($message)
                    ->setNew(false)
            );
        }

        return $catalogue;
    }
}