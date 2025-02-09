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

class FileSource implements SourceInterface
{
    public function __construct(
        private string $path,
        private int|null $line = null,
        private int|null $column = null,
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getLine(): int|null
    {
        return $this->line;
    }

    public function getColumn(): int|null
    {
        return $this->column;
    }

    public function equals(SourceInterface $source): bool
    {
        if (!$source instanceof FileSource) {
            return false;
        }

        if ($this->path !== $source->getPath()) {
            return false;
        }

        if ($this->line !== $source->getLine()) {
            return false;
        }

        return $this->column === $source->getColumn();
    }

    public function __toString(): string
    {
        $str = $this->path;

        if (null !== $this->line) {
            $str .= ' on line ' . $this->line;

            if (null !== $this->column) {
                $str .= ' at column ' . $this->column;
            }
        }

        return $str;
    }
}
