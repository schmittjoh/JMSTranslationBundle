<?php

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
            'Output-Path: '.$outputDir."\n"
           .'Directories: '.$inputDir."\n"
           .'Excluded Directories: Tests'."\n"
           .'Excluded Names: *Test.php, *TestCase.php'."\n"
           .'Output-Format: # whatever is present, if nothing then xliff #'."\n"
           .'Custom Extractors: # none #'."\n"
           .'============================================================'."\n"
           .'Writing translation file "'.$outputDir.'/messages.en.xliff".'."\n"
           .'done!'."\n"
        ;

        $this->getApp()->run($input, $output = new Output());
        $this->assertEquals($expectedOutput, $output->getContent());

        $files = FileUtils::findTranslationFiles($outputDir);
        $this->assertTrue(isset($files['messages']['en']));
    }
}