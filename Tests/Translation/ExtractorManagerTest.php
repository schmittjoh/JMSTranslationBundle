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

namespace JMS\TranslationBundle\Tests\Translation;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Tests\BaseTestCase;
use Psr\Log\NullLogger;
use JMS\TranslationBundle\Translation\Extractor\FileExtractor;
use JMS\TranslationBundle\Translation\ExtractorManager;

class ExtractorManagerTest extends BaseTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage There is no extractor with alias "foo". Available extractors: # none #
     */
    public function testSetEnabledCustomExtractorsThrowsExceptionWhenAliasInvalid()
    {
        $manager = $this->getManager();
        $manager->setEnabledExtractors(array('foo' => true));
    }

    public function testOnlySomeExtractorsEnabled()
    {
        $foo = $this->createMock('JMS\TranslationBundle\Translation\ExtractorInterface');
        $foo
            ->expects($this->never())
            ->method('extract')
        ;

        $catalogue = new MessageCatalogue();
        $catalogue->add(new Message('foo'));
        $bar = $this->createMock('JMS\TranslationBundle\Translation\ExtractorInterface');
        $bar
            ->expects($this->once())
            ->method('extract')
            ->will($this->returnValue($catalogue))
        ;

        $manager = $this->getManager(null, array(
            'foo' => $foo,
            'bar' => $bar,
        ));
        $manager->setEnabledExtractors(array('bar' => true));

        $this->assertEquals($catalogue, $manager->extract());
    }

    public function testReset()
    {
        $foo = $this->createMock('JMS\TranslationBundle\Translation\ExtractorInterface');
        $logger = new NullLogger();

        $extractor = new FileExtractor(new \Twig_Environment(new \Twig_Loader_Array(array())), $logger, array());
        $extractor->setExcludedNames(array('foo', 'bar'));
        $extractor->setExcludedDirs(array('baz'));

        $manager = $this->getManager($extractor, array(
            'foo' => $foo,
        ));
        $manager->setEnabledExtractors(array('foo' => true));
        $manager->setDirectories(array('/'));

        $managerReflection   = new \ReflectionClass($manager);
        $extractorReflection = new \ReflectionClass($extractor);

        $enabledExtractorsProperty = $managerReflection->getProperty('enabledExtractors');
        $enabledExtractorsProperty->setAccessible(true);

        $directoriesProperty = $managerReflection->getProperty('directories');
        $directoriesProperty->setAccessible(true);

        $excludedNamesProperty = $extractorReflection->getProperty('excludedNames');
        $excludedNamesProperty->setAccessible(true);

        $excludedDirsProperty = $extractorReflection->getProperty('excludedDirs');
        $excludedDirsProperty->setAccessible(true);

        $this->assertEquals(array('foo' => true), $enabledExtractorsProperty->getValue($manager));
        $this->assertEquals(array('/'), $directoriesProperty->getValue($manager));
        $this->assertEquals(array('foo', 'bar'), $excludedNamesProperty->getValue($extractor));
        $this->assertEquals(array('baz'), $excludedDirsProperty->getValue($extractor));

        $manager->reset();

        $this->assertEquals(array(), $enabledExtractorsProperty->getValue($manager));
        $this->assertEquals(array(), $directoriesProperty->getValue($manager));
        $this->assertEquals(array(), $excludedNamesProperty->getValue($extractor));
        $this->assertEquals(array(), $excludedDirsProperty->getValue($extractor));
    }

    private function getManager(FileExtractor $extractor = null, array $extractors = array())
    {
        $logger = new NullLogger();

        if (null === $extractor) {
            $extractor = new FileExtractor(new \Twig_Environment(new \Twig_Loader_Array(array())), $logger, array());
        }

        return new ExtractorManager($extractor, $logger, $extractors);
    }
}
