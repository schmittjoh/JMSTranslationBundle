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

use SplFileInfo;

class FileSource implements SourceInterface
{
    private $file;
    private $line;
    private $column;

    public function __construct(SplFileInfo $file, $line = null, $column = null)
    {
        $this->file = $file;
        $this->line = $line;
        $this->column = $column;
    }

    public function getPath()
    {
        $parts = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $this->getRealPath()));
        $parts = array_splice($parts, -3);

        return implode('/', $parts);
    }

    public function getRealPath()
    {
        return $this->file->getRealPath();
    }

    public function hasLine()
    {
        return null !== $this->line;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function hasColumn()
    {
        return null !== $this->column;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function equals(SourceInterface $source)
    {
        if (!$source instanceof FileSource) {
            return false;
        }

        if ($this->getPath() !== $source->getPath()) {
            return false;
        }

        if ($this->line !== $source->getLine()) {
            return false;
        }

        if ($this->column !== $source->getColumn()) {
            return false;
        }

        return true;
    }
}