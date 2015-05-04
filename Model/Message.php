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
class Message
{
    /** Unique ID of this message (same across the same domain) */
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

    /**
     * @static
     * @param $id
     * @param string $domain
     * @return Message
     */
    public static function forThisFile($id, $domain = 'messages')
    {
        $message = new static($id, $domain);

        $trace = debug_backtrace(false);
        if (isset($trace[0]['file'])) {
            $message->addSource(new FileSource($trace[0]['file']));
        }

        return $message;
    }

    /**
     * @static
     * @param $id
     * @param string $domain
     * @return Message
     */
    public static function create($id, $domain = 'messages')
    {
        return new static($id, $domain);
    }

    /**
     * @param $id
     * @param string $domain
     */
    public function __construct($id, $domain = 'messages')
    {
        $this->id = $id;
        $this->domain = $domain;
    }

    /**
     * @param SourceInterface $source
     * @return Message
     */
    public function addSource(SourceInterface $source)
    {
        if ($this->hasSource($source)) {
            return $this;
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
        return $this->localeString !== null ? $this->localeString : ($this->new ? ($this->desc !== null ? $this->desc : $this->id) : '');
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

    public function setSources(array $sources = array()) {
        $this->sources = $sources;

        return $this;
    }

    /**
     * Merges an extracted message.
     *
     * Do not use this if you want to merge a message from an existing catalogue.
     * In these cases, use mergeExisting() instead.
     *
     * @param Message $message
     * @throws RuntimeException
     */
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
            $this->localeString = null;
            if ($localeString = $message->getLocaleString()) {
                $this->localeString = $localeString;
            }
        }

        foreach ($message->getSources() as $source) {
            $this->addSource($source);
        }

        $this->setNew($message->isNew());
        if ($localeString = $message->getLocaleString()) {
            $this->localeString = $localeString;
        }
    }

    /**
     * Merges a message from an existing translation catalogue.
     *
     * Do not use this if you want to merge a message from an extracted catalogue.
     * In these cases, use merge() instead.
     *
     * @param Message $message
     * @deprecated not in use atm
     */
    public function mergeExisting(Message $message)
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

        $this->setNew($message->isNew());
        if ($localeString = $message->getLocaleString()) {
            $this->localeString = $localeString;
        }
    }

    /**
     * Merge a scanned message into an extising message.
     *
     * This method does essentially the same as {@link mergeExisting()} but with reversed operands.
     * Whereas {@link mergeExisting()} is used to merge an existing message into a scanned message (this),
     * {@link mergeScanned()} is used to merge a scanned message into an existing message (this).
     * The result of both methods is the same, except that the result will end up in the existing message,
     * instead of the scanned message, so extra information read from the existing message is not discarded.
     *
     * @param Message $message
     * @author Dieter Peeters <peetersdiet@gmail.com>
     */
    public function mergeScanned(Message $message) {
        if ($this->id !== $message->getId()) {
            throw new RuntimeException(sprintf('You can only merge messages with the same id. Expected id "%s", but got "%s".', $this->id, $message->getId()));
        }

        if (null === $this->getMeaning()) {
            $this->meaning = $message->getMeaning();
        }

        if (null === $this->getDesc()) {
            $this->desc = $message->getDesc();
        }

        $this->sources = array();
        foreach ($message->getSources() as $source) {
            $this->addSource($source);
        }

        if (!$this->getLocaleString()) {
            $this->localeString = $message->getLocaleString();
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
