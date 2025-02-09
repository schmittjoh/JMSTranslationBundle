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

namespace JMS\TranslationBundle\Util;

use JMS\TranslationBundle\Exception\RuntimeException;

/**
 * A writer implementation.
 *
 * This may be used to simplify writing well-formatted code.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Writer
{
    /**
     * @var int
     */
    public $indentationSpaces = 4;

    /**
     * @var int
     */
    public $indentationLevel = 0;

    /**
     * @var string
     */
    public $content = '';

    /**
     * @var int
     */
    public $changeCount = 0;

    private array $changes = [];

    /**
     * @return $this
     */
    public function indent()
    {
        $this->indentationLevel += 1;

        return $this;
    }

    /**
     * @return $this
     */
    public function outdent()
    {
        $this->indentationLevel -= 1;

        if ($this->indentationLevel < 0) {
            throw new RuntimeException('The identation level cannot be less than zero.');
        }

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function writeln($content)
    {
        $this->write($content . "\n");

        return $this;
    }

    public function revert()
    {
        $change = array_pop($this->changes);
        $this->changeCount -= 1;
        $this->content = substr($this->content, 0, -1 * strlen($change));
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function write($content)
    {
        $contentEndsWithNewLine = "\n" === substr($this->content, -1);
        $addition = '';

        $lines = explode("\n", $content);
        for ($i = 0, $c = count($lines); $i < $c; $i++) {
            if (
                $this->indentationLevel > 0
                && !empty($lines[$i])
                && ((empty($addition) && "\n" === substr($this->content, -1)) || "\n" === substr($addition, -1))
            ) {
                $addition .= str_repeat(' ', $this->indentationLevel * $this->indentationSpaces);
            }

            $addition .= $lines[$i];

            if ($i + 1 < $c) {
                $addition .= "\n";
            }
        }

        $this->content .= $addition;
        $this->changes[] = $addition;
        $this->changeCount += 1;

        return $this;
    }

    /**
     * @param bool $preserveNewLines
     *
     * @return $this
     */
    public function rtrim($preserveNewLines = true)
    {
        if (!$preserveNewLines) {
            $this->content = rtrim($this->content);

            return $this;
        }

        $addNl = "\n" === substr($this->content, -1);
        $this->content = rtrim($this->content);

        if ($addNl) {
            $this->content .= "\n";
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->content = '';
        $this->indentationLevel = 0;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
