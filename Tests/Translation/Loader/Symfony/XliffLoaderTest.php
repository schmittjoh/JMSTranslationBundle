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

namespace JMS\TranslationBundle\Tests\Translation\Loader\Symfony;

use JMS\TranslationBundle\Exception\InvalidArgumentException;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Translation\MessageCatalogue;
use JMS\TranslationBundle\Translation\Loader\Symfony\XliffLoader;

class XliffLoaderTest extends BaseLoaderTest
{
    public function testLoadOldFormat()
    {
        $expected = new MessageCatalogue('en');
        $expected->add(array(
            'foo1' => 'bar',
            'foo2' => 'bar',
            'foo3' => 'bar',
            'foo4' => 'bar',
        ));

        $file = __DIR__.'/xliff/old_format.xml';
        $expected->addResource(new FileResource($file));

        $this->assertEquals($expected, $this->getLoader()->load($file, 'en'));
    }

    protected function getInputFile($key)
    {
        if (!is_file($file = __DIR__.'/../../Dumper/xliff/'.$key.'.xml')) {
            throw new InvalidArgumentException(sprintf('The input file for key "%s" does not exist.', $key));
        }

        return $file;
    }

    protected function getLoader()
    {
        return new XliffLoader();
    }
}
