<?php

declare(strict_types=1);

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
use JMS\TranslationBundle\Model\SourceInterface;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testCreate(): void
    {
        $message = Message::create('id', 'foo');

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('id', $message->getId());
        $this->assertEquals('foo', $message->getDomain());
    }

    public function testForThisFile(): void
    {
        $message = Message::forThisFile('foo', 'bar');

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('foo', $message->getId());
        $this->assertEquals('bar', $message->getDomain());

        $source = new FileSource(__FILE__);
        $this->assertTrue($message->hasSource($source));
    }

    public function testGetId(): void
    {
        $message = new Message('foo');
        $this->assertEquals('foo', $message->getId());
    }

    public function testGetDomain(): void
    {
        $message = new Message('foo', 'bar');
        $this->assertEquals('bar', $message->getDomain());
    }

    public function testGetDesc(): void
    {
        $message = new Message('foo');
        $this->assertNull($message->getDesc());

        $this->assertSame($message, $message->setDesc('foo'));
        $this->assertEquals('foo', $message->getDesc());
    }

    public function testGetMeaning(): void
    {
        $message = new Message('foo');
        $this->assertNull($message->getMeaning());

        $this->assertSame($message, $message->setMeaning('foo'));
        $this->assertEquals('foo', $message->getMeaning());
    }

    public function testGetSources(): void
    {
        $message = new Message('foo');
        $this->assertEquals([], $message->getSources());

        $this->assertSame($message, $message->addSource($source = $this->createMock(SourceInterface::class)));
        $this->assertSame([$source], $message->getSources());
        $this->assertSame($message, $message->setSources([$source2 = $this->createMock(SourceInterface::class)]));
        $this->assertSame([$source2], $message->getSources());
    }

    public function testMerge(): void
    {
        $message = new Message('foo');
        $message->setDesc('foo');
        $message->setMeaning('foo');
        $message->addSource($s1 = $this->createMock(SourceInterface::class));

        $message2 = new Message('foo');
        $message2->setDesc('bar');
        $message2->addSource($s2 = $this->createMock(SourceInterface::class));

        $message->merge($message2);

        $this->assertEquals('bar', $message->getDesc());
        $this->assertEquals('foo', $message->getMeaning());
        $this->assertSame([$s1, $s2], $message->getSources());
    }

    public function testMergeRememberDesc(): void
    {
        $message = new Message('foo_id');
        $message->setDesc('foo_desc');
        $message->setMeaning('foo_meaning');
        $message->addSource($s1 = $this->createMock(SourceInterface::class));

        $message2 = new Message('foo_id');
        $message2->setMeaning('bar_meaning');
        $message2->addSource($s2 = $this->createMock(SourceInterface::class));

        $message->merge($message2);

        $this->assertEquals('foo_desc', $message->getDesc());
        $this->assertEquals('bar_meaning', $message->getMeaning());
        $this->assertSame([$s1, $s2], $message->getSources());
    }

    public function testMergeExisting(): void
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
        $this->assertEquals([], $message->getSources());
    }

    public function testMergeScanned(): void
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
        $this->assertEquals([], $message->getSources());
    }

    public function testGetIsNew(): void
    {
        $message = new Message('foo');

        $this->assertTrue($message->isNew());
        $this->assertSame($message, $message->setNew(false));
        $this->assertFalse($message->isNew());
    }

    public function testToString(): void
    {
        $message = new Message('foo');
        $this->assertEquals('foo', (string) $message);
    }

    public function hasSource(): void
    {
        $message = new Message('foo');

        $s2 = $this->createMock(SourceInterface::class);

        $s1 = $this->createMock(SourceInterface::class);
        $s1
            ->expects($this->once())
            ->method('equals')
            ->with($s2)
            ->willReturn(true);

        $message->addSource($s1);
        $this->assertTrue($message->hasSource($s2));
    }

    public function testGetLocaleString(): void
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
