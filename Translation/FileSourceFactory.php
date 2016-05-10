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

namespace JMS\TranslationBundle\Translation;

use JMS\TranslationBundle\Model\FileSource;

class FileSourceFactory
{
    /**
     * @var string
     */
    protected $kernelRoot;

    /**
     * FileSourceFactory constructor.
     * @param string $kernelRoot
     */
    public function __construct($kernelRoot)
    {
        $this->kernelRoot = $kernelRoot;
    }

    /**
     * Generate a new FileSource with a relative path
     * @param          $path string
     * @param null|int $line
     * @param null|int $column
     * @return FileSource
     */
    public function create($path, $line = null, $column = null)
    {
        if (0 === strpos($path, $this->kernelRoot)) {
            $path = substr($path, strlen($this->kernelRoot));
        }
        $path = str_replace($this->kernelRoot, '', $path);

        return new FileSource($path, $line, $column);
    }
}