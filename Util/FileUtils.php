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

namespace JMS\TranslationBundle\Util;

use Symfony\Component\Finder\Finder;

abstract class FileUtils
{
    /**
     * Returns the available translation files.
     *
     * The returned array has the structure
     *
     *    array(
     *        'domain' => array(
     *            'locale' => array(
     *                array('format', \SplFileInfo)
     *            )
     *        )
     *    )
     *
     * @throws \RuntimeException
     * @return array
     */
    public static function findTranslationFiles($directory)
    {
        $files = array();
        foreach (Finder::create()->in($directory)->files() as $file) {
            if (!preg_match('/^([^\.]+)\.([^\.]+)\.([^\.]+)$/', basename($file), $match)) {
                continue;
            }

            $files[$match[1]][$match[2]] = array(
                $match[3],
                $file
            );
        }
        
        uksort($files, 'strcasecmp');
        
        return $files;
    }

    /**
     * Search for the file with the same name, in all subdirectories, and return its full file name. If not found, we assume it will be put directly in the directory
     *
     * @param $directory
     * @param $domainName
     * @param $locale
     * @param $fileFormat
     * @return string
     */
    public static function computeTranslationFile($directory, $domainName, $locale, $fileFormat) {
        $fileName = "$domainName.$locale.$fileFormat";

        foreach (Finder::create()->in($directory)->files()->name($fileName) as $file) {
            return $file;
        }
        return "$directory/$fileName";
    }

    private final function __construct() { }
}
