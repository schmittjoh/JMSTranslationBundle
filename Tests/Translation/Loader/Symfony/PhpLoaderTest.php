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

namespace JMS\TranslationBundle\Tests\Translation\Loader\Symfony;

use JMS\TranslationBundle\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Loader\PhpFileLoader;

class PhpLoaderTest extends LoaderTestCase
{
    protected function getLoader(): PhpFileLoader
    {
        return new PhpFileLoader();
    }

    protected function getInputFile($key)
    {
        $fileRealPath =  __DIR__ . '/../../Dumper/php/' . $key . '.php';
        if (! is_file($fileRealPath)) {
            throw new InvalidArgumentException(sprintf('The input file for key "%s" does not exist.', $key));
        }

        return $fileRealPath;
    }
}
