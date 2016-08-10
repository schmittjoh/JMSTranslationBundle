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
     *
     * @param string $kernelRoot
     */
    public function __construct($kernelRoot)
    {
        $this->kernelRoot = $kernelRoot;
    }

    /**
     * Generate a new FileSource with a relative path.
     *
     * @param \SplFileInfo $file
     * @param null|int     $line
     * @param null|int     $column
     *
     * @return FileSource
     */
    public function create(\SplFileInfo $file, $line = null, $column = null)
    {
        return new FileSource($this->getRelativePath((string) $file), $line, $column);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getRelativePath($path)
    {
        if (0 === strpos($path, $this->kernelRoot)) {
            return substr($path, strlen($this->kernelRoot));
        }

        $relativePath = $ds = DIRECTORY_SEPARATOR;
        $rootArray = explode($ds, $this->kernelRoot);
        $pathArray = explode($ds, $path);

        // Take the first directory in the kernelRoot tree
        foreach ($rootArray as $rootCurrentDirectory) {
            // Take the first directory from the path tree
            $pathCurrentDirectory = array_shift($pathArray);

            // If they are not equal
            if ($pathCurrentDirectory !== $rootCurrentDirectory) {
                // Prepend $relativePath with "/.."
                $relativePath = $ds.'..'.$relativePath;

                if ($pathCurrentDirectory) {
                    // Append the current directory
                    $relativePath .= $pathCurrentDirectory.$ds;
                }
            }
        }

        // Add the rest of the $pathArray on the relative directory
        return rtrim($relativePath.implode($ds, $pathArray), '/');
    }
}
