<?php
namespace JMS\TranslationBundle\Translation\Extractor;

interface DomainAwareInterface
{
    /**
     * @param string $domain
     */
    public function setDomain($domain);
}
