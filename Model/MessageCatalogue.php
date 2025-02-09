<?php

declare(strict_types=1);

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
    private string|null $locale = null;

    /** @var array<string, MessageCollection> */
    private array $domains = [];

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

    public function add(Message $message)
    {
        $this
            ->getOrCreateDomain($message->getDomain())
            ->add($message);
    }

    public function set(Message $message, $force = false)
    {
        $this
            ->getOrCreateDomain($message->getDomain())
            ->set($message, $force);
    }

    /**
     * @param string $id
     * @param string $domain
     *
     * @return Message
     *
     * @throws InvalidArgumentException
     */
    public function get($id, $domain = 'messages')
    {
        return $this->getDomain($domain)->get($id);
    }

    /**
     * @return bool
     */
    public function has(Message $message)
    {
        if (!$this->hasDomain($message->getDomain())) {
            return false;
        }

        return $this->getDomain($message->getDomain())->has($message->getId());
    }

    public function merge(MessageCatalogue $catalogue)
    {
        foreach ($catalogue->getDomains() as $name => $domainCatalogue) {
            $this->getOrCreateDomain($name)->merge($domainCatalogue);
        }
    }

    /**
     * @param string $domain
     *
     * @return bool
     */
    public function hasDomain($domain)
    {
        return isset($this->domains[$domain]);
    }

    /**
     * @param string $domain
     *
     * @return MessageCollection
     */
    public function getDomain($domain)
    {
        if (!$this->hasDomain($domain)) {
            throw new InvalidArgumentException(sprintf('There is no domain with name "%s".', $domain));
        }

        return $this->domains[$domain];
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }

    private function getOrCreateDomain(string $domain): MessageCollection
    {
        return $this->domains[$domain] ??= new MessageCollection($this);
    }
}
