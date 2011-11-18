<?php

namespace JMS\TranslationBundle\Translation\Loader;

use JMS\TranslationBundle\Model\FileSource;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageDomain;

class XliffLoader implements LoaderInterface
{
    public function load($resource, $locale, $domain = 'messages')
    {
        $previous = libxml_use_internal_errors(true);
        if (false === $doc = simplexml_load_file($resource)) {
            libxml_use_internal_errors($previous);

            throw new \RuntimeException(sprintf('Could not load XML-file "%s": %s', $resource, libxml_get_last_error()));
        }
        libxml_use_internal_errors($previous);

        $doc->registerXPathNamespace('xliff', 'urn:oasis:names:tc:xliff:document:1.2');
        $doc->registerXPathNamespace('jms', 'urn:jms:translation');

        $hasReferenceFiles = in_array('urn:jms:translation', $doc->getNamespaces(true));

        $domain = new MessageDomain($domain, $locale);

        foreach ($doc->xpath('//xliff:trans-unit') as $trans) {
            $id = ($resName = (string) $trans->attributes()->resname)
                       ? $resName : (string) $trans->source;

            $m = Message::create($id, $domain->getName())
                    ->setDesc((string) $trans->source)
                    ->setLocaleString((string) $trans->target)
            ;
            $domain->add($m);

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

            if (!($state = (string) $trans->target->attributes()->state)
                    || 'new' !== $state) {
                $m->setNew(false);
            }

        }

        return $domain;
    }
}