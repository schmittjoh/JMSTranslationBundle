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

namespace JMS\TranslationBundle\Tests\Translation\Dumper;

use JMS\TranslationBundle\Exception\InvalidArgumentException;
use JMS\TranslationBundle\Translation\Dumper\XliffDumper;

class XliffDumperTest extends BaseDumperTest
{
    protected function getDumper()
    {
        $dumper = new XliffDumper();
        $dumper->setAddDate(false);

        return $dumper;
    }

    protected function getOutput($key)
    {
        if (!is_file($file = __DIR__.'/xliff/'.$key.'.xml')) {
            throw new InvalidArgumentException(sprintf('There is no output for key "%s".', $key));
        }

        // This is very slow for some reason
//         $doc = \DOMDocument::load($file);
//         $this->assertTrue($doc->schemaValidate(__DIR__.'/../../../Resources/schema/xliff-core-1.2-strict.xsd'));

        return file_get_contents($file);
    }
}