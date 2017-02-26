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

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Translation\MessageCatalogue;

abstract class BaseLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadSimple()
    {
        $expected = new MessageCatalogue('en');
        $expected->add(array('foo' => 'foo'));

        $file = $this->getInputFile('simple');
        $expected->addResource(new FileResource($file));

        $this->assertEquals($expected, $this->load($file));
    }

    public function testLoadStructureWithMetadata()
    {
        $expected = new MessageCatalogue('en');
        $expected->add(array(
            'foo.bar.baz' => 'Foo',
            'foo.bar.moo' => 'foo.bar.moo',
            'foo.baz' => 'foo.baz',
        ));

        $file = $this->getInputFile('structure_with_metadata');
        $expected->addResource(new FileResource($file));

        $this->assertEquals($expected, $this->load($file));
    }

    public function testLoadStructure()
    {
        $expected = new MessageCatalogue('en');
        $expected->add(array(
            'foo.bar.baz' => 'foo.bar.baz',
        ));

        $file = $this->getInputFile('structure');
        $expected->addResource(new FileResource($file));

        $this->assertEquals($expected, $this->load($file));
    }

    public function testLoadWithMetadata()
    {
        $expected = new MessageCatalogue('en');
        $expected->add(array(
            'foo' => 'bar',
        ));

        $file = $this->getInputFile('with_metadata');
        $expected->addResource(new FileResource($file));

        $this->assertEquals($expected, $this->load($file));
    }

    abstract protected function getLoader();
    abstract protected function getInputFile($key);

    private function load($file, $locale = 'en')
    {
        return $this->getLoader()->load($file, $locale);
    }
}
