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

use JMS\TranslationBundle\Exception\RuntimeException;

/**
 * Represents an _extracted_ message.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class Message
{
    /** Unique ID of this message (same across all translations) */
    private $id;

    private $new = true;

    private $domain;

    private $localeString;

    /** Additional information about the intended meaning */
    private $meaning;

    /** The description/sample for translators */
    private $desc;

    /** The sources where this message occurs */
    private $sources = array();

    public static function forThisFile($id, $domain = 'messages')
    {
        $message = new self($id, $domain);

        $trace = debug_backtrace(false);
        if (isset($trace[0]['file'])) {
            $message->addSource(new FileSource($trace[0]['file']));
        }

        return $message;
    }

    public static function create($id, $domain = 'messages')
    {
        return new self($id, $domain);
    }

    public function __construct($id, $domain = 'messages')
    {
        $this->id = $id;
        $this->domain = $domain;
    }

    public function addSource(SourceInterface $source)
    {
        if ($this->hasSource($source)) {
            return;
        }

        $this->sources[] = $source;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function isNew()
    {
        return $this->new;
    }

    public function getLocaleString()
    {
        return $this->localeString ?: ($this->desc ?: $this->id);
    }

    /**
     * Returns the string from which to translate.
     *
     * This typically is the description, but we will fallback to the id
     * if that has not been given.
     *
     * @return string
     */
    public function getSourceString()
    {
        return $this->desc ?: $this->id;
    }

    public function getMeaning()
    {
        return $this->meaning;
    }

    public function getDesc()
    {
        return $this->desc;
    }

    public function getSources()
    {
        return $this->sources;
    }

    public function setMeaning($meaning)
    {
        $this->meaning = $meaning;

        return $this;
    }

    public function setNew($bool)
    {
        $this->new = (Boolean) $bool;

        return $this;
    }

    public function setDesc($desc)
    {
        $this->desc = $desc;

        return $this;
    }

    public function setLocaleString($str)
    {
        $this->localeString = $str;

        return $this;
    }

    public function merge(Message $message)
    {
        if ($this->id !== $message->getId()) {
            throw new RuntimeException(sprintf('You can only merge messages with the same id. Expected id "%s", but got "%s".', $this->id, $message->getId()));
        }

        if (null !== $meaning = $message->getMeaning()) {
            $this->meaning = $meaning;
        }

        if (null !== $desc = $message->getDesc()) {
            $this->desc = $desc;
        }

        foreach ($message->getSources() as $source) {
            $this->addSource($source);
        }
    }

    public function hasSource(SourceInterface $source)
    {
        foreach ($this->sources as $cSource) {
            if ($cSource->equals($source)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Allows us to use this with existing message catalogues.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }
}