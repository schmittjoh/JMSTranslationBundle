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
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class MessageDomainCatalogue
{
    private $name;

    private $locale;

    private $messages = array();

    /**
     * @param $name
     */
    public function __construct($name, $locale)
    {
        $this->name = $name;
        $this->locale = $locale;
    }

    /**
     * @param Message $message
     */
    public function add(Message $message)
    {
        if (isset($this->messages[$id = $message->getId()])) {
            $this->messages[$id]->merge($message);
        } else {
            $this->messages[$id] = $message;
        }
    }

    /**
     * @param Message $message
     */
    public function set(Message $message)
    {
        $this->messages[$message->getId()] = $message;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \JMS\TranslationBundle\Exception\InvalidArgumentException
     */
    public function get($id)
    {
        if (!isset($this->messages[$id])) {
            throw new \InvalidArgumentException(sprintf('There is no message with id "%s".', $id));
        }

        return $this->messages[$id];
    }

    /**
     * @param $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->messages[$id]);
    }

    /**
     * @param $callback
     * @throws \JMS\TranslationBundle\Exception\InvalidArgumentException
     */
    public function sort($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf('$callback must be a valid callback.'));
        }

        uasort($this->messages, $callback);
    }

    /**
     * @param $callback
     * @throws \JMS\TranslationBundle\Exception\InvalidArgumentException
     */
    public function filter($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf('$callback must be a valid callback.'));
        }

        $this->messages = array_filter($this->messages, $callback);
    }

    /**
     * @param array $messages
     */
    public function replace(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->messages;
    }

    /**
     * @param MessageDomainCatalogue $domain
     */
    public function merge(MessageDomainCatalogue $domain)
    {
        foreach ($domain->all() as $id => $message) {
            $this->add($message);
        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }
}