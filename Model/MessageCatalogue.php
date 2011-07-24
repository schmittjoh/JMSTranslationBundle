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

    private $messages = array();

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function add(Message $message)
    {
        if (isset($this->messages[$id = $message->getId()])) {
            $this->messages[$id]->merge($message);
        } else {
            $this->messages[$id] = $message;
        }
    }

    public function set(Message $message)
    {
        $this->messages[$message->getId()] = $message;
    }

    public function has($id)
    {
        return isset($this->messages[$id]);
    }

    public function sort($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf('$callback must be a valid callback.'));
        }

        uasort($this->messages, $callback);
    }

    public function filter($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf('$callback must be a valid callback.'));
        }

        $this->messages = array_filter($this->messages, $callback);
    }

    public function replace(array $messages)
    {
        $this->messages = $messages;
    }

    public function all()
    {
        return $this->messages;
    }

    public function merge(MessageCatalogue $catalogue)
    {
        foreach ($catalogue->all() as $id => $message) {
            $this->add($message);
        }
    }
}