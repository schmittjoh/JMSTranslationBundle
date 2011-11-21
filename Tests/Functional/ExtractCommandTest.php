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

namespace JMS\TranslationBundle\Tests\Functional;

use JMS\TranslationBundle\Command\ExtractTranslationCommand;
use JMS\TranslationBundle\Util\FileUtils;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArgvInput;

class ExtractCommandTest extends BaseCommandTestCase
{
    public function testExtract()
    {
        $input = new ArgvInput(array(
            'app/console',
            'translation:extract',
            'en',
            '--dir='.($inputDir = __DIR__.'/../Translation/Extractor/Fixture/SimpleTest'),
            '--output-dir='.($outputDir = sys_get_temp_dir().'/'.uniqid('extract'))
        ));

        $expectedOutput =
            'Keep old translations: No'."\n"
           .'Output-Path: '.$outputDir."\n"
           .'Directories: '.$inputDir."\n"
           .'Excluded Directories: Tests'."\n"
           .'Excluded Names: *Test.php, *TestCase.php'."\n"
           .'Output-Format: # whatever is present, if nothing then xliff #'."\n"
           .'Custom Extractors: # none #'."\n"
           .'============================================================'."\n"
           .'Loading catalogues from "'.$outputDir.'"'."\n"
           .'Extracting translation keys'."\n"
           .'Extracting messages from directory : '.$inputDir."\n"
           .'Writing translation file "'.$outputDir.'/messages.en.xliff".'."\n"
           .'done!'."\n"
        ;

        $this->getApp()->run($input, $output = new Output());
        $this->assertEquals($expectedOutput, $output->getContent());

        $files = FileUtils::findTranslationFiles($outputDir);
        $this->assertTrue(isset($files['messages']['en']));
    }
}