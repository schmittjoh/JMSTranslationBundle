<?php

namespace JMS\TranslationBundle\Translation;

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

            throw new \RuntimeException(sprintf('Could not load XLIFF file "%s": %s', $file, libxml_get_last_error()->getMessage()));
        }
        libxml_use_internal_errors($previous);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('xliff', 'urn:oasis:names:tc:xliff:document:1.2');

        $list = $xpath->query("//xliff:trans-unit[@id='".str_replace("'", "\\'", $id)."']");
        if (null === $unit = $list->item(0)) {
            throw new \RuntimeException(sprintf('Could not find id "%s".', $id));
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