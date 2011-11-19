<?php

namespace JMS\TranslationBundle\Translation\Loader;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageDomainCatalogue;
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
     * @return MessageDomainCatalogue
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $domain = new MessageDomainCatalogue($domain, $locale);

        foreach ($this->loader->load($resource, 'en', $domain)->all() as $id => $message) {
            $domain->add(
                Message::create($id, $domain->getName())
                    ->setLocaleString($message)
                    ->setNew(false)
            );
        }

        return $domain;
    }
}