<?php

namespace JMS\TranslationBundle\Translation\Loader;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
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
     * @return MessageCatalogue
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        $catalogue = new MessageCatalogue();

        foreach ($this->loader->load($resource, 'en', $domain)
                    ->all() as $id => $message) {
            $catalogue->add(
                Message::create($id, $domain)
                    ->setLocaleString($message)
                    ->setNew(false)
            );
        }

        return $catalogue;
    }
}