<?php

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
        foreach (Finder::create()->in($directory)->depth('< 1')->files() as $file) {
            if (!preg_match('/^([^\.]+)\.([^\.]+)\.([^\.]+)$/', basename($file), $match)) {
                continue;
            }

            $files[$match[1]][$match[2]] = array(
                $match[3],
                $file
            );
        }

        return $files;
    }

    private final function __construct() { }
}