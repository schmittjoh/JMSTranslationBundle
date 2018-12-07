<?php

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\File\ValidationContextExtractor;

class ValidationContextExtractorTest extends BasePhpFileExtractorTest
{
    public function testExtractValidationMessages()
    {
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/MyEntity.php');


        $expected = new MessageCatalogue();

        $message = new Message('entity.default');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 15));
        $expected->add($message);

        $message = new Message('entity.fully-qualified');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 22));
        $expected->add($message);

        $message = new Message('entity.custom-domain', 'custom-domain');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 29));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyEntity.php'));
    }

    protected function getDefaultExtractor()
    {
        return new ValidationContextExtractor($this->getFileSourceFactory());
    }
}
