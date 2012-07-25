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
use JMS\TranslationBundle\Model\MessageCatalogue;

/**
 * Writes translation files.
 *
 * This implementation is a bit more advanced than that of the Translation component
 * in that it may also write the description, meaning and occurrences of translation
 * ids.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class FileWriter
{
    private $dumpers;

    /**
     * @param array $dumpers
     */
    public function __construct(array $dumpers = array())
    {
        $this->dumpers = $dumpers;
    }

    /**
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $domain
     * @param $filePath
     * @param $format
     * @throws \JMS\TranslationBundle\Exception\InvalidArgumentException
     */
    public function write(MessageCatalogue $catalogue, $domain, $filePath, $format)
    {
        if (!isset($this->dumpers[$format])) {
            throw new InvalidArgumentException(sprintf('The format "%s" is not supported.', $format));
        }

        // sort messages before dumping
        $catalogue->getDomain($domain)->sort(function($a, $b) {
            return strcmp($a->getId(), $b->getId());
        });

        file_put_contents($filePath, $this->dumpers[$format]->dump($catalogue, $domain));
    }
}