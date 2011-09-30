<?php

namespace JMS\TranslationBundle\Tests\Translation\Loader;

use JMS\TranslationBundle\Translation\Dumper\XliffDumper;

use JMS\TranslationBundle\Translation\Loader\XliffLoader;

class XliffLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTestFiles
     */
    public function testLoadIntegration($file)
    {
        $loader = new XliffLoader();
        $catalogue = $loader->load($file, 'en');

        $dumper = new XliffDumper();
        $dumper->setAddDate(false);

        $this->assertEquals(file_get_contents($file), $dumper->dump($catalogue));
    }

    public function getTestFiles()
    {
        $files = array();
        $files[] = array(__DIR__.'/../Dumper/xliff/simple.xml');
        $files[] = array(__DIR__.'/../Dumper/xliff/structure_with_metadata.xml');
        $files[] = array(__DIR__.'/../Dumper/xliff/structure.xml');
        $files[] = array(__DIR__.'/../Dumper/xliff/with_metadata.xml');

        return $files;
    }
}