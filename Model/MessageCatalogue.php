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

namespace JMS\TranslationBundle\Model;

use JMS\TranslationBundle\Exception\InvalidArgumentException;

/**
 * Represents a collection of **extracted** messages.
 *
 * A catalogue may consist of multiple domains. Each message belongs to
 * a specific domain, and the ID of the message is uniquely identifying the
 * message in its domain, but **not** across domains.
 *
 * This catalogue is only used for extraction, for translation at run-time
 * we still use the optimized catalogue from the Translation component.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class MessageCatalogue
{
    private $locale;
    private $domains = array();

    /**
     * @param $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param Message $message
     */
    public function add(Message $message)
    {
        $this
            ->getOrCreateDomain($message->getDomain())
            ->add($message)
        ;
    }

    /**
     * @param Message $message
     */
    public function set(Message $message, $force = false)
    {
        $this
            ->getOrCreateDomain($message->getDomain())
            ->set($message, $force)
        ;
    }

    /**
     * @param $id
     * @param $domain
     * @throws \JMS\TranslationBundle\Exception\InvalidArgumentException
     * @return Message
     */
    public function get($id, $domain = 'messages')
    {
        return $this->getDomain($domain)->get($id);
    }

    /**
     * @param Message $message
     * @return Boolean
     */
    public function has(Message $message)
    {
        if (!$this->hasDomain($message->getDomain())) {
            return false;
        }

        return $this->getDomain($message->getDomain())->has($message->getId());
    }

    /**
     * @param MessageCatalogue $catalogue
     */
    public function merge(MessageCatalogue $catalogue)
    {
        foreach ($catalogue->getDomains() as $name => $domainCatalogue) {
            $this->getOrCreateDomain($name)->merge($domainCatalogue);
        }
    }

    /**
     * @param string $domain
     * @return Boolean
     */
    public function hasDomain($domain)
    {
        return isset($this->domains[$domain]);
    }

    /**
     * @param string $domain
     * @return MessageCollection
     */
    public function getDomain($domain)
    {
        if (!$this->hasDomain($domain)) {
            throw new InvalidArgumentException(sprintf('There is no domain with name "%s".', $domain));
        }

        return $this->domains[$domain];
    }

    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @param string $domain
     * @return MessageCollection
     */
    private function getOrCreateDomain($domain)
    {
        if (!$this->hasDomain($domain)) {
            $this->domains[$domain] = new MessageCollection($this);
        }

        return $this->domains[$domain];
    }
}