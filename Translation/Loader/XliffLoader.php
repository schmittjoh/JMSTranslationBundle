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
    /**
     * @param mixed $resource
     * @param string $locale
     * @param string $domain
     * @return MessageCatalogue
     */
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

        /** @var \SimpleXMLElement $trans */
        foreach ($doc->xpath('//xliff:trans-unit') as $trans) {
            $id = ($resName = (string) $trans->attributes()->resname)
                       ? $resName : (string) $trans->source;

            /** @var Message $m */
            $m = Message::create($id, $domain)
                    ->setDesc((string) $trans->source)
                    ->setLocaleString((string) $trans->target)
            ;

            $m->setApproved($trans['approved']=='yes');

            if (isset($trans->target['state'])) {
                $m->setState((string) $trans->target['state']);
            }

            // Create closure
            $addNoteToMessage = function(Message $m, $note) {
                $m->addNote((string) $note, isset($note['from']) ? ((string) $note['from']) : null);
            };

            // If the translation has a note
            if (isset($trans->note)) {
                // If we have more than one note. We can't use is_array becuase $trans->note is a \SimpleXmlElement
                if (count($trans->note) > 1) {
                    foreach ($trans->note as $note) {
                        $addNoteToMessage($m, $note);
                    }
                } else {
                    $addNoteToMessage($m, $trans->note);
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
