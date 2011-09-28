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

class XliffMessageUpdater
{
    public function update($file, $id, $trans)
    {
        $previous = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->formatOutput = true;
        $doc->preserveWhiteSpace = false;
        if (false === @$doc->load($file, LIBXML_COMPACT)) {
            libxml_use_internal_errors($previous);

            throw new RuntimeException(sprintf('Could not load XLIFF file "%s": %s', $file, libxml_get_last_error()->getMessage()));
        }
        libxml_use_internal_errors($previous);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('xliff', 'urn:oasis:names:tc:xliff:document:1.2');

        $list = $xpath->query("//xliff:trans-unit[@id='".str_replace("'", "\\'", $id)."']");
        if (null === $unit = $list->item(0)) {
            throw new RuntimeException(sprintf('Could not find id "%s".', $id));
        }

        $list = $xpath->query('./xliff:target', $unit);
        if (null !== $target = $list->item(0)) {
            $unit->removeChild($target);
        }

        $unit->appendChild($target = $doc->createElement('target'));
        $target->appendChild($doc->createTextNode($trans));

        $doc->save($file);
    }
}