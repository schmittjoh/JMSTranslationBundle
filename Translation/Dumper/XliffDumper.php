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

namespace JMS\TranslationBundle\Translation\Dumper;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\JMSTranslationBundle;
use JMS\TranslationBundle\Model\MessageCatalogue;

/**
 * XLIFF dumper.
 *
 * This dumper uses version 1.2 of the specification.
 *
 * @see http://docs.oasis-open.org/xliff/v1.2/os/xliff-core.html
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class XliffDumper implements DumperInterface
{
    private $sourceLanguage = 'en';
    private $addDate = true;

    /**
     * @param $bool
     */
    public function setAddDate($bool)
    {
        $this->addDate = (Boolean) $bool;
    }

    /**
     * @param $lang
     */
    public function setSourceLanguage($lang)
    {
        $this->sourceLanguage = $lang;
    }

    /**
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $domain
     * @return string
     */
    public function dump(MessageCatalogue $catalogue, $domain = 'messages')
    {
        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;

        $doc->appendChild($root = $doc->createElement('xliff'));
        $root->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');
        $root->setAttribute('xmlns:jms', 'urn:jms:translation');
        $root->setAttribute('version', '1.2');

        $root->appendChild($file = $doc->createElement('file'));

        if ($this->addDate) {
            $date = new \DateTime();
            $file->setAttribute('date', $date->format('Y-m-d\TH:i:s\Z'));
        }

        $file->setAttribute('source-language', $this->sourceLanguage);
        $file->setAttribute('target-language', $catalogue->getLocale());
        $file->setAttribute('datatype', 'plaintext');
        $file->setAttribute('original', 'not.available');

        $file->appendChild($header = $doc->createElement('header'));

        $header->appendChild($tool = $doc->createElement('tool'));
        $tool->setAttribute('tool-id', 'JMSTranslationBundle');
        $tool->setAttribute('tool-name', 'JMSTranslationBundle');
        $tool->setAttribute('tool-version', JMSTranslationBundle::VERSION);


        $header->appendChild($note = $doc->createElement('note'));
        $note->appendChild($doc->createTextNode('The source node in most cases contains the sample message as written by the developer. If it looks like a dot-delimitted string such as "form.label.firstname", then the developer has not provided a default message.'));

        $file->appendChild($body = $doc->createElement('body'));

        foreach ($catalogue->getDomain($domain)->all() as $id => $message) {
            $body->appendChild($unit = $doc->createElement('trans-unit'));
            $unit->setAttribute('id', hash('sha1', $id));
            $unit->setAttribute('resname', $id);

            $unit->appendChild($source = $doc->createElement('source'));
            if (preg_match('/[<>&]/', $message->getSourceString())) {
                $source->appendChild($doc->createCDATASection($message->getSourceString()));
            } else {
                $source->appendChild($doc->createTextNode($message->getSourceString()));
            }

            $unit->appendChild($target = $doc->createElement('target'));
            if (preg_match('/[<>&]/', $message->getLocaleString())) {
                $target->appendChild($doc->createCDATASection($message->getLocaleString()));
            } else {
                $target->appendChild($doc->createTextNode($message->getLocaleString()));
            }

            if ($message->isNew()) {
                $target->setAttribute('state', 'new');
            }

            // As per the OASIS XLIFF 1.2 non-XLIFF elements must be at the end of the <trans-unit>
            if ($sources = $message->getSources()) {
                foreach ($sources as $source) {
                    if ($source instanceof FileSource) {
                        $unit->appendChild($refFile = $doc->createElement('jms:reference-file', $source->getPath()));

                        if ($source->getLine()) {
                            $refFile->setAttribute('line', $source->getLine());
                        }

                        if ($source->getColumn()) {
                            $refFile->setAttribute('column', $source->getColumn());
                        }

                        continue;
                    }

                    $unit->appendChild($doc->createElementNS('jms:reference', (string) $source));
                }
            }

            if ($meaning = $message->getMeaning()) {
                $unit->setAttribute('extradata', 'Meaning: '.$meaning);
            }

        }

        return $doc->saveXML();
    }
}