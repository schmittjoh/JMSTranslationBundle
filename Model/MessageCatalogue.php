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
 * Represents a collection of _extracted_ messages.
 *
 * This catalogue is only used for extraction, for translation at run-time
 * we still use the optimized catalogue from the Translation component.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class MessageCatalogue
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
    public function addMessage(Message $message)
    {
        $this
            ->getOrCreateDomain($message->getDomain())
            ->add($message)
        ;
    }

    /**
     * @param Message $message
     */
    public function setMessage(Message $message)
    {
        $this
            ->getOrCreateDomain($message->getDomain())
            ->set($message)
        ;
    }

    /**
     * @param string $domain
     * @return MessageDomainCatalogue
     */
    public function getOrCreateDomain($domain)
    {
        if (!$this->hasDomain($domain)) {
            $this->domains[$domain] = new MessageDomainCatalogue($domain, $this->getLocale());
        }

        return $this->domains[$domain];
    }

    /**
     * @param $domain
     * @return bool
     */
    public function hasDomain($domain)
    {
        return isset($this->domains[$domain]);
    }

    /**
     * @param $domain
     * @return MessageDomainCatalogue
     */
    public function getDomain($domain)
    {
        if (!$this->hasDomain($domain)) {
            throw new \InvalidArgumentException(sprintf('There is no domain with name "%s".', $domain));
        }

        return $this->domains[$domain];
    }

    /**
     * @param $id
     * @param $domain
     * @throws \JMS\TranslationBundle\Exception\InvalidArgumentException
     * @return Message
     */
    public function getMessage($id, $domain = 'messages')
    {
        return $this->getDomain($domain)->get($id);
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function hasMessage(Message $message)
    {
        if (!$this->hasDomain($message->getDomain())) {
            return false;
        }

        return $this->getDomain($message->getDomain())->has($message->getId());
    }

    /**
     * @param array $domains
     */
    public function replace(array $domains)
    {
        $this->domains = $domains;
    }

    /**
     * @param $domain
     * @param $messages
     */
    public function replaceMessages($domain, $messages)
    {
        $this->getDomain($domain)->replace($messages);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->domains;
    }

    /**
     * @param MessageCatalogue $catalogue
     */
    public function merge(MessageCatalogue $catalogue)
    {
        foreach ($catalogue->all() as $name => $domain) {
            $this->getOrCreateDomain($name)->merge($domain);
        }
    }
}