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

namespace JMS\TranslationBundle\Tests\Functional\Command;

use JMS\TranslationBundle\Util\FileUtils;
use Symfony\Component\Console\Input\ArgvInput;

class ExtractCommandTest extends BaseCommandTestCase
{
    public function testExtract(): void
    {
        $input = new ArgvInput([
            'app/console',
            'jms:translation:extract',
            'en',
            '--dir=' . $inputDir = __DIR__ . '/../../Translation/Extractor/Fixture/SimpleTest',
            '--output-dir=' . ($outputDir = sys_get_temp_dir() . '/' . uniqid('extract')),
        ]);

        $expectedOutput =
            'Extracting Translations for locale en' . "\n"
           . 'Keep old translations: No' . "\n"
           . 'Output-Path: ' . $outputDir . "\n"
           . 'Directories: ' . $inputDir . "\n"
           . 'Excluded Directories: Tests' . "\n"
           . 'Excluded Names: *Test.php, *TestCase.php' . "\n"
           . 'Output-Format: # whatever is present, if nothing then xlf #' . "\n"
           . 'Custom Extractors: # none #' . "\n"
           . '============================================================' . "\n"
           . 'Loading catalogues from "' . $outputDir . '"' . "\n"
           . 'Extracting translation keys' . "\n"
           . 'Extracting messages from directory : ' . $inputDir . "\n"
           . 'Writing translation file "' . $outputDir . '/messages.en.xlf".' . "\n"
           . 'done!' . "\n";

        $this->getApp()->run($input, $output = new Output());
        $this->assertEquals($expectedOutput, $output->getContent());

        $files = FileUtils::findTranslationFiles($outputDir);
        $this->assertTrue(isset($files['messages']['en']));
    }

    public function testExtractDryRun(): void
    {
        $input = new ArgvInput([
            'app/console',
            'jms:translation:extract',
            'en',
            '--dir=' . $inputDir = __DIR__ . '/../../Translation/Extractor/Fixture/SimpleTest',
            '--output-dir=' . ($outputDir = sys_get_temp_dir() . '/' . uniqid('extract')),
            '--dry-run',
            '--verbose',
        ]);

        $expectedOutput = [
            'php.foo->',
            'php.bar-> Bar',
            'php.baz->',
            'php.foo_bar-> Foo',
            'twig.foo->',
            'twig.bar-> Bar',
            'twig.baz->',
            'twig.foo_bar-> Foo',
            'form.foo->',
            'form.bar->',
            'controller.foo-> Foo',
        ];

        $this->getApp()->run($input, $output = new Output());

        foreach ($expectedOutput as $transID) {
            $this->assertStringContainsString($transID, $output->getContent());
        }
    }
}
