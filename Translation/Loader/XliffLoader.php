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

use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message\XliffMessage as Message;

class XliffLoader implements LoaderInterface
{
    public function load($resource, $locale, $domain = 'messages')
    {
        $previous = libxml_use_internal_errors(true);
        if (false === $doc = simplexml_load_file($resource)) {
            libxml_use_internal_errors($previous);
            $libxmlError = libxml_get_last_error();

            throw new RuntimeException(sprintf('Could not load XML-file "%s": %s', $resource, $libxmlError->message));
        }
        libxml_use_internal_errors($previous);

        $doc->registerXPathNamespace('xliff', 'urn:oasis:names:tc:xliff:document:1.2');
        $doc->registerXPathNamespace('jms', 'urn:jms:translation');

        $hasReferenceFiles = in_array('urn:jms:translation', $doc->getNamespaces(true));

        $catalogue = new MessageCatalogue();
        $catalogue->setLocale($locale);

        foreach ($doc->xpath('//xliff:trans-unit') as $trans) {
            $id = ($resName = (string) $trans->attributes()->resname)
                       ? $resName : (string) $trans->source;

            $m = Message::create($id, $domain)
                    ->setDesc((string) $trans->source)
                    ->setLocaleString((string) $trans->target)
            ;

            $m->setApproved($trans['approved']=='yes');

            if (isset($trans->target['state'])) {
                $m->setState($trans->target['state']);
            }

            if (isset($trans->note)) {
                if (count($trans->note) > 1) {
                    foreach ($trans->note as $note) {
                        $m->addNote((string) $note, isset($note['from']) ? ((string) $note['from']) : null);
                    }
                } else {
                    $m->addNote((string) $trans->note, isset($trans->note['from']) ? ((string) $trans->note['from']) : null);
                }
            }

            $catalogue->add($m);

            if ($hasReferenceFiles) {
                foreach ($trans->xpath('./jms:reference-file') as $file) {
                    $line = (string) $file->attributes()->line;
                    $column = (string) $file->attributes()->column;
                    $m->addSource(new FileSource(
                        (string) $file,
                        $line ? (integer) $line : null,
                        $column ? (integer) $column : null
                    ));
                }
            }

            if ($meaning = (string) $trans->attributes()->extradata) {
                if (0 === strpos($meaning, 'Meaning: ')) {
                    $meaning = substr($meaning, 9);
                }

                $m->setMeaning($meaning);
            }

            if (!($state = (string) $trans->target->attributes()->state) || 'new' !== $state) {
                $m->setNew(false);
            }

        }

        return $catalogue;
    }
}