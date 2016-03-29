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

namespace JMS\TranslationBundle\Tests\Model;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;

class MessageCatalogueTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $catalogue = new MessageCatalogue();
        $catalogue->add($m = new Message('foo'));

        $this->assertTrue($catalogue->hasDomain('messages'));
        $this->assertEquals(array('foo' => $m), $catalogue->getDomain('messages')->all());
    }

    public function testGet()
    {
        $catalogue = new MessageCatalogue();
        $catalogue->add($message = Message::create('foo'));

        $this->assertTrue($catalogue->hasDomain('messages'));
        $this->assertSame($message, $catalogue->get('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetThrowsExceptionWhenMessageDoesNotExist()
    {
        $catalogue = new MessageCatalogue();
        $catalogue->getDomain('foo');
    }

    public function testGetSetLocale()
    {
        $catalogue = new MessageCatalogue();
        $this->assertNull($catalogue->getLocale());

        $catalogue->setLocale('en');
        $this->assertEquals('en', $catalogue->getLocale());
    }

    public function testHasDomain()
    {
        $catalogue = new MessageCatalogue();
        $this->assertFalse($catalogue->hasDomain('messages'));

        $catalogue->add(new Message('foo'));
        $this->assertTrue($catalogue->hasDomain('messages'));
    }

    public function testGetDomain()
    {
        $catalogue = new MessageCatalogue();
        $catalogue->add(new Message('foo'));

        $col = $catalogue->getDomain('messages');
        $this->assertInstanceOf('JMS\TranslationBundle\Model\MessageCollection', $col);
        $this->assertEquals(array('foo'), array_keys($col->all()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetDomainWhenDomainDoesNotExist()
    {
        $catalogue = new MessageCatalogue();
        $catalogue->getDomain('messages');
    }

    public function testGetDomains()
    {
        $cat = new MessageCatalogue();
        $cat->add(new Message('foo'));
        $cat->add(new Message('foo', 'foo'));

        $this->assertEquals(array('messages', 'foo'), array_keys($domains = $cat->getDomains()));
        $this->assertInstanceOf('JMS\TranslationBundle\Model\MessageCollection', $domains['foo']);
    }

    public function testMerge()
    {
        $cat = new MessageCatalogue();
        $cat->add(new Message('foo', 'foo'));

        $cat2 = new MessageCatalogue();
        $cat2->add(new Message('foo', 'bar'));

        $cat->merge($cat2);

        $this->assertEquals(array('foo', 'bar'), array_keys($domains = $cat->getDomains()));
        $this->assertEquals(array('bar'), array_keys($cat2->getDomains()));

        $this->assertEquals(array('foo'), array_keys($domains['foo']->all()));
        $this->assertEquals(array('foo'), array_keys($domains['bar']->all()));
    }
}
