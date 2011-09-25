<?php

namespace JMS\TranslationBundle\Tests\Translation;

use JMS\TranslationBundle\Model\Message;

use JMS\TranslationBundle\Model\MessageCatalogue;

use Symfony\Component\HttpKernel\Log\NullLogger;

use JMS\TranslationBundle\Translation\Extractor\FileExtractor;
use JMS\TranslationBundle\Translation\ExtractorManager;

class ExtractorManagerTest extends \PHPUnit_Framework_TestCase
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
        $foo = $this->getMock('JMS\TranslationBundle\Translation\ExtractorInterface');
        $foo
            ->expects($this->never())
            ->method('extract')
        ;

        $catalogue = new MessageCatalogue();
        $catalogue->add(new Message('foo'));
        $bar = $this->getMock('JMS\TranslationBundle\Translation\ExtractorInterface');
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

    private function getManager(FileExtractor $extractor = null, array $extractors = array())
    {
        $logger = new NullLogger();

        if (null === $extractor) {
            $extractor = new FileExtractor(new \Twig_Environment(), $logger, array());
        }

        return new ExtractorManager($extractor, $logger, $extractors);
    }
}