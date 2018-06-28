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

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Tests\BaseTestCase;

class MessageTest extends BaseTestCase
{
    public function testCreate()
    {
        $message = Message::create('id', 'foo');

        $this->assertInstanceOf('JMS\TranslationBundle\Model\Message', $message);
        $this->assertEquals('id', $message->getId());
        $this->assertEquals('foo', $message->getDomain());
    }

    public function testForThisFile()
    {
        $message = Message::forThisFile('foo', 'bar');

        $this->assertInstanceOf('JMS\TranslationBundle\Model\Message', $message);
        $this->assertEquals('foo', $message->getId());
        $this->assertEquals('bar', $message->getDomain());

        $source = new FileSource(__FILE__);
        $this->assertTrue($message->hasSource($source));
    }

    public function testGetId()
    {
        $message = new Message('foo');
        $this->assertEquals('foo', $message->getId());
    }

    public function testGetDomain()
    {
        $message = new Message('foo', 'bar');
        $this->assertEquals('bar', $message->getDomain());
    }

    public function testGetDesc()
    {
        $message = new Message('foo');
        $this->assertNull($message->getDesc());

        $this->assertSame($message, $message->setDesc('foo'));
        $this->assertEquals('foo', $message->getDesc());
    }

    public function testGetMeaning()
    {
        $message = new Message('foo');
        $this->assertNull($message->getMeaning());

        $this->assertSame($message, $message->setMeaning('foo'));
        $this->assertEquals('foo', $message->getMeaning());
    }

    public function testGetSources()
    {
        $message = new Message('foo');
        $this->assertEquals(array(), $message->getSources());

        $this->assertSame($message, $message->addSource($source = $this->createMock('JMS\TranslationBundle\Model\SourceInterface')));
        $this->assertSame(array($source), $message->getSources());
        $this->assertSame($message, $message->setSources(array($source2 = $this->createMock('JMS\TranslationBundle\Model\SourceInterface'))));
        $this->assertSame(array($source2), $message->getSources());
    }

    public function testMerge()
    {
        $message = new Message('foo');
        $message->setDesc('foo');
        $message->setMeaning('foo');
        $message->addSource($s1 = $this->createMock('JMS\TranslationBundle\Model\SourceInterface'));

        $message2 = new Message('foo');
        $message2->setDesc('bar');
        $message2->addSource($s2 = $this->createMock('JMS\TranslationBundle\Model\SourceInterface'));

        $message->merge($message2);

        $this->assertEquals('bar', $message->getDesc());
        $this->assertEquals('foo', $message->getMeaning());
        $this->assertSame(array($s1, $s2), $message->getSources());
    }

    public function testMergeRememberDesc()
    {
        $message = new Message('foo_id');
        $message->setDesc('foo_desc');
        $message->setMeaning('foo_meaning');
        $message->addSource($s1 = $this->createMock('JMS\TranslationBundle\Model\SourceInterface'));

        $message2 = new Message('foo_id');
        $message2->setMeaning('bar_meaning');
        $message2->addSource($s2 = $this->createMock('JMS\TranslationBundle\Model\SourceInterface'));

        $message->merge($message2);

        $this->assertEquals('foo_desc', $message->getDesc());
        $this->assertEquals('bar_meaning', $message->getMeaning());
        $this->assertSame(array($s1, $s2), $message->getSources());
    }

    public function testMergeExisting()
    {
        $message = new Message('foo');
        $message->setDesc('bar');

        $existingMessage = new Message('foo');
        $existingMessage->setLocaleString('foobar');
        $existingMessage->setNew(false);
        $existingMessage->addSource(new FileSource('foo/bar'));

        $message->mergeExisting($existingMessage);

        $this->assertEquals('bar', $message->getDesc());
        $this->assertEquals('foobar', $message->getLocaleString());
        $this->assertFalse($message->isNew());
        $this->assertEquals(array(), $message->getSources());
    }

    public function testMergeScanned()
    {
        $message = new Message('foo');
        $message->setLocaleString('foobar');
        $message->setNew(false);
        $message->addSource(new FileSource('foo/bar'));

        $scannedMessage = new Message('foo');
        $scannedMessage->setDesc('foobar');

        $message->mergeScanned($scannedMessage);

        $this->assertEquals('foobar', $message->getDesc());
        $this->assertEquals('foobar', $message->getLocaleString());
        $this->assertFalse($message->isNew());
        $this->assertEquals(array(), $message->getSources());
    }

    public function testGetIsNew()
    {
        $message = new Message('foo');

        $this->assertTrue($message->isNew());
        $this->assertSame($message, $message->setNew(false));
        $this->assertFalse($message->isNew());
    }

    public function testToString()
    {
        $message = new Message('foo');
        $this->assertEquals('foo', (string) $message);
    }

    public function hasSource()
    {
        $message = new Message('foo');

        $s2 = $this->createMock('JMS\TranslationBundle\Model\SourceInterface');

        $s1 = $this->createMock('JMS\TranslationBundle\Model\SourceInterface');
        $s1
            ->expects($this->once())
            ->method('equals')
            ->with($s2)
            ->will($this->returnValue(true))
        ;

        $message->addSource($s1);
        $this->assertTrue($message->hasSource($s2));
    }

    public function testGetLocaleString()
    {
        $message = new Message('foo');
        $message->setDesc('bar');
        $message->setNew(true);

        $existingMessage = new Message('foo');
        $existingMessage->setDesc('bar');
        $existingMessage->setNew(false);

        $this->assertEquals($message->getDesc(), $message->getLocaleString());
        $this->assertEquals('', $existingMessage->getLocaleString());
    }
}
