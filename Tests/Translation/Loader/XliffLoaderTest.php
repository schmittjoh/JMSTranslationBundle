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

namespace JMS\TranslationBundle\Tests\Translation\Loader;

use JMS\TranslationBundle\Model\Message\XliffMessage;
use JMS\TranslationBundle\Model\Message\XliffMessageState;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Dumper\XliffDumper;
use JMS\TranslationBundle\Translation\Loader\XliffLoader;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class XliffLoaderTest extends TestCase
{
    #[DataProvider('getTestFiles')]
    public function testLoadIntegration(string $file): void
    {
        $loader    = new XliffLoader();
        $catalogue = $loader->load($file, 'en');

        $dumper = new XliffDumper();
        $dumper->setAddDate(false);

        $this->assertStringEqualsFile($file, $dumper->dump($catalogue));
    }

    public function testLoadWithSymfonyFormat(): void
    {
        $loader = new XliffLoader();

        $expected = new MessageCatalogue();
        $expected->setLocale('en');
        $expected->add(XliffMessage::create('foo1')
            ->setDesc('foo1')->setLocaleString('bar')->setNew(false));
        $expected->add(XliffMessage::create('foo2')
            ->setDesc('foo2')->setLocaleString('bar')->setNew(false));
        $expected->add(XliffMessage::create('foo3')
            ->setDesc('foo3')->setLocaleString('bar')->setNew(false));
        $expected->add(XliffMessage::create('foo4')
            ->setDesc('foo4')->setLocaleString('bar')->setNew(false));

        $this->assertEquals(
            $expected,
            $loader->load(__DIR__ . '/Symfony/xliff/old_format.xml', 'en')
        );
    }

    public function testWorkflowAttributes(): void
    {
        $loader = new XliffLoader();

        $expected = new MessageCatalogue();
        $expected->setLocale('en');
        $expected->add(XliffMessage::create('foo1')
            ->setDesc('foo1')
            ->setLocaleString('bar')
            ->setState(XliffMessageState::STATE_NEEDS_ADAPTATION)
            ->setApproved(true));

        $this->assertEquals(
            $expected,
            $loader->load(__DIR__ . '/Symfony/xliff/workflow.xml', 'en')
        );
    }

    public static function getTestFiles(): array
    {
        $files   = [];
        $files[] = [__DIR__ . '/../Dumper/xliff/simple.xml'];
        $files[] = [__DIR__ . '/../Dumper/xliff/structure_with_metadata.xml'];
        $files[] = [__DIR__ . '/../Dumper/xliff/structure.xml'];
        $files[] = [__DIR__ . '/../Dumper/xliff/with_metadata.xml'];

        return $files;
    }
}
