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
use JMS\TranslationBundle\Translation\Dumper\XliffDumper;

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
    /**
     * @var array
     */
    private $dumpers;

    /**
     * @param array $dumpers
     */
    public function __construct(array $dumpers = array())
    {
        $this->dumpers = $dumpers;
    }

    /**
     * Writes a message catalogue to file
     *
     * @param MessageCatalogue $catalogue
     * @param string $domain
     * @param string $filePath
     * @param string $format
     * @param array $outputOptions
     *
     * @throws InvalidArgumentException
     */
    public function write(MessageCatalogue $catalogue, $domain, $filePath, $format, $outputOptions)
    {
        if (!isset($this->dumpers[$format])) {
            throw new InvalidArgumentException(sprintf('The format "%s" is not supported.', $format));
        }

        // sort messages before dumping
        $catalogue->getDomain($domain)->sort(function($a, $b) {
            return strcmp($a->getId(), $b->getId());
        });
        
        $dumper = $this->dumpers[$format];
        
        if ($dumper instanceof XliffDumper) {
            if (isset($outputOptions['add_date'])) {
                $dumper->setAddDate($outputOptions['add_date']);
            }
            if (isset($outputOptions['add_filerefs'])) {
                $dumper->setAddFileRefs($outputOptions['add_filerefs']);
            }
        }
        
        file_put_contents($filePath, $dumper->dump($catalogue, $domain, $filePath));
    }
}
