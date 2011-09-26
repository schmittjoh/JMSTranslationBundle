<?php

namespace JMS\TranslationBundle\Tests\Translation;

use JMS\TranslationBundle\Translation\XliffMessageUpdater;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Dumper\XliffDumper;

class XliffMessageUpdaterTest extends \PHPUnit_Framework_TestCase
{
    private $file;

    public function testUpdate()
    {
        $updater = new XliffMessageUpdater();
        $updater->update($this->file, 'foo', 'bar');

        $this->assertEquals(file_get_contents(__DIR__.'/Fixture/xliff_updated.xml'), file_get_contents($this->file));
    }

    protected function setUp()
    {
        $catalogue = new MessageCatalogue();
        $catalogue->setLocale('en');
        $catalogue->add(new Message('foo'));

        $message = new Message('bar');
        $message->setDesc('This is a bar.');
        $message->setLocaleString('This is a bar.');
        $message->addSource(new FileSource(__FILE__));
        $catalogue->add($message);

        $dumper = new XliffDumper();
        $dumper->setAddDate(false);

        $this->file = tempnam(sys_get_temp_dir(), 'xliff_message_updater');
        file_put_contents($this->file, $dumper->dump($catalogue));
    }

    protected function tearDown()
    {
        @unlink($this->file);
    }
}