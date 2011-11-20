<?php

namespace JMS\TranslationBundle\Tests\Translation\Loader;

use JMS\TranslationBundle\Translation\Loader\SymfonyLoaderAdapter;
use Symfony\Component\Translation\MessageCatalogue;

class SymfonyLoaderAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $symfonyCatalogue = new MessageCatalogue('en');
        $symfonyCatalogue->add(array('foo' => 'bar'));
        
        $symfonyLoader = $this->getMock('Symfony\Component\Translation\Loader\LoaderInterface');
        $symfonyLoader->expects($this->once())
            ->method('load')
            ->with('foo', 'en', 'messages')
            ->will($this->returnValue($symfonyCatalogue));
        
        $adapter = new SymfonyLoaderAdapter($symfonyLoader);
        $bundleCatalogue = $adapter->load('foo', 'en', 'messages');
        $this->assertInstanceOf('JMS\TranslationBundle\Model\MessageCatalogue', $bundleCatalogue);
        $this->assertEquals('en', $bundleCatalogue->getLocale());
        $this->assertTrue($bundleCatalogue->hasDomain('messages'));
        $this->assertTrue($bundleCatalogue->getDomain('messages')->has('foo'));
        
        $message = $bundleCatalogue->getDomain('messages')->get('foo');
        $this->assertEquals('bar', $message->getLocaleString());
        $this->assertFalse($message->isNew());
    }
}