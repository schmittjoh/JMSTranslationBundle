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

namespace JMS\TranslationBundle\Tests\Translation;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileExtractor;
use JMS\TranslationBundle\Translation\ExtractorInterface;
use JMS\TranslationBundle\Translation\ExtractorManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class ExtractorManagerTest extends TestCase
{
    public function testSetEnabledCustomExtractorsThrowsExceptionWhenAliasInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('There is no extractor with alias "foo". Available extractors: # none #');

        $manager = $this->getManager();
        $manager->setEnabledExtractors(['foo' => true]);
    }

    public function testOnlySomeExtractorsEnabled()
    {
        $foo = $this->createMock(ExtractorInterface::class);
        $foo
            ->expects($this->never())
            ->method('extract');

        $catalogue = new MessageCatalogue();
        $catalogue->add(new Message('foo'));
        $bar = $this->createMock(ExtractorInterface::class);
        $bar
            ->expects($this->once())
            ->method('extract')
            ->willReturn($catalogue);

        $manager = $this->getManager(null, [
            'foo' => $foo,
            'bar' => $bar,
        ]);
        $manager->setEnabledExtractors(['bar' => true]);

        $this->assertEquals($catalogue, $manager->extract());
    }

    public function testReset()
    {
        $foo    = $this->createMock(ExtractorInterface::class);
        $logger = new NullLogger();

        $extractor = new FileExtractor(new Environment(new ArrayLoader([])), $logger, []);
        $extractor->setExcludedNames(['foo', 'bar']);
        $extractor->setExcludedDirs(['baz']);

        $manager = $this->getManager($extractor, ['foo' => $foo]);
        $manager->setEnabledExtractors(['foo' => true]);
        $manager->setDirectories(['/']);

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

        $this->assertEquals(['foo' => true], $enabledExtractorsProperty->getValue($manager));
        $this->assertEquals(['/'], $directoriesProperty->getValue($manager));
        $this->assertEquals(['foo', 'bar'], $excludedNamesProperty->getValue($extractor));
        $this->assertEquals(['baz'], $excludedDirsProperty->getValue($extractor));

        $manager->reset();

        $this->assertEquals([], $enabledExtractorsProperty->getValue($manager));
        $this->assertEquals([], $directoriesProperty->getValue($manager));
        $this->assertEquals([], $excludedNamesProperty->getValue($extractor));
        $this->assertEquals([], $excludedDirsProperty->getValue($extractor));
    }

    private function getManager(?FileExtractor $extractor = null, array $extractors = [])
    {
        $logger = new NullLogger();

        if ($extractor === null) {
            $extractor = new FileExtractor(new Environment(new ArrayLoader([])), $logger, []);
        }

        return new ExtractorManager($extractor, $logger, $extractors);
    }
}
