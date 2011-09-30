<?php

namespace JMS\TranslationBundle\Tests\Translation\Loader;

use JMS\TranslationBundle\Model\Message;

use JMS\TranslationBundle\Model\MessageCatalogue;

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

    public function testLoadWithSymfonyFormat()
    {
        $loader = new XliffLoader();

        $expected = new MessageCatalogue();
        $expected->setLocale('en');
        $expected->add(Message::create('foo1')
            ->setDesc('foo1')->setLocaleString('bar')->setNew(false));
        $expected->add(Message::create('foo2')
            ->setDesc('foo2')->setLocaleString('bar')->setNew(false));
        $expected->add(Message::create('foo3')
            ->setDesc('foo3')->setLocaleString('bar')->setNew(false));
        $expected->add(Message::create('foo4')
            ->setDesc('foo4')->setLocaleString('bar')->setNew(false));

        $this->assertEquals(
            $expected,
            $loader->load(__DIR__.'/Symfony/xliff/old_format.xml', 'en')
        );
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