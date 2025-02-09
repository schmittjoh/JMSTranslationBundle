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
use JMS\TranslationBundle\Model\MessageCollection;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

class MessageCollectionTest extends TestCase
{
    public function testAdd(): void
    {
        $domain = new MessageCollection();
        $domain->add($m = new Message('foo'));

        $this->assertSame(['foo' => $m], $domain->all());
    }

    public function testAddMerges(): void
    {
        $m2 = $this->createMock(Message::class);

        $m1 = $this->createMock(Message::class);
        $m1->expects($this->once())
            ->method('merge')
            ->with($m2);

        $col = new MessageCollection();
        $col->add($m1);
        $col->add($m2);
    }

    public function testGet(): void
    {
        $domain = new MessageCollection();
        $domain->add($message = Message::create('foo'));

        $this->assertTrue($domain->has('foo'));
        $this->assertSame($message, $domain->get('foo'));
    }

    public function testGetThrowsExceptionWhenMessageDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $catalogue = new MessageCollection();
        $catalogue->get('foo');
    }

    public function testSet(): void
    {
        $col = new MessageCollection();
        $col->add($m = Message::create('foo'));

        $this->assertTrue($col->has('foo'));
        $this->assertSame($m, $col->get('foo'));
    }

    public function testSetDoesNotMerge(): void
    {
        $m2 = $this->createMock(Message::class);
        $m2
            ->method('getId')
            ->willReturn('foo');

        $m1 = $this->createMock(Message::class);
        $m1->expects($this->never())
            ->method('merge');
        $m1
            ->method('getId')
            ->willReturn('foo');

        $col = new MessageCollection();
        $col->set($m1);
        $col->set($m2);

        $this->assertSame($m2, $col->get('foo'));
    }

    public function testSort(): void
    {
        $col = new MessageCollection();
        $col->add(new Message('b'));
        $col->add(new Message('c'));
        $col->add(new Message('a'));

        $this->assertEquals(['b', 'c', 'a'], array_keys($col->all()));

        $col->sort('strcasecmp');
        $this->assertEquals(['a', 'b', 'c'], array_keys($col->all()));
    }

    public function testFilter(): void
    {
        $col = new MessageCollection();
        $col->add($m = new Message('a'));
        $col->add(new Message('b'));
        $col->add(new Message('c'));
        $col->filter(static function ($v) {
            return $v->getId() === 'a';
        });

        $this->assertEquals(['a'], array_keys($col->all()));
        $this->assertSame($m, $col->get('a'));
    }

    public function testMerge(): void
    {
        $col = new MessageCollection();
        $col->add(new Message('a'));

        $col2 = new MessageCollection();
        $col2->add(new Message('b'));

        $col->merge($col2);
        $this->assertEquals(['a', 'b'], array_keys($col->all()));
    }

    public function testAddChecksConsistency(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The message \'a\' exists with two different descs: \'a\' in foo on line 1, and \'b\' in bar on line 2');

        $col = new MessageCollection();

        $msg = new Message('a');
        $msg->setDesc('a');
        $msg->addSource(new FileSource('foo', 1));

        $msg2 = new Message('a');
        $msg2->setDesc('b');
        $msg2->addSource(new FileSource('bar', 2));

        $col->add($msg);
        $col->add($msg2);
    }

    #[DoesNotPerformAssertions()]
    public function testAddChecksConsistencyButAllowsEmptyDescs(): void
    {
        $col = new MessageCollection();

        // both message have not desc

        $msg  = new Message('a');
        $msg2 = new Message('a');

        $col->add($msg);
        $col->add($msg2);

        // first message have a desc

        $msg = new Message('b');
        $msg->setDesc('b');

        $msg2 = new Message('b');

        $col->add($msg);
        $col->add($msg2);

        // second message have a desc

        $msg = new Message('c');

        $msg2 = new Message('c');
        $msg2->setDesc('c');

        $col->add($msg);
        $col->add($msg2);

        // non-null empty descs

        $msg = new Message('d');
        $msg->setDesc('d');

        $msg2 = new Message('d');
        $msg2->setDesc('');

        $col->add($msg);
        $col->add($msg2);
    }

    public function testSetChecksConsistency(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The message \'a\' exists with two different descs: \'a\' in foo on line 1, and \'b\' in bar on line 2');

        $col = new MessageCollection();

        $msg = new Message('a');
        $msg->setDesc('a');
        $msg->addSource(new FileSource('foo', 1));

        $msg2 = new Message('a');
        $msg2->setDesc('b');
        $msg2->addSource(new FileSource('bar', 2));

        $col->set($msg);
        $col->set($msg2);
    }

    #[DoesNotPerformAssertions()]
    public function testSetChecksConsistencyButAllowsEmptyDescs(): void
    {
        $col = new MessageCollection();

        // both message have not desc

        $msg  = new Message('a');
        $msg2 = new Message('a');

        $col->set($msg);
        $col->set($msg2);

        // first message have a desc

        $msg = new Message('b');
        $msg->setDesc('b');

        $msg2 = new Message('b');

        $col->set($msg);
        $col->set($msg2);

        // second message have a desc

        $msg = new Message('c');

        $msg2 = new Message('c');
        $msg2->setDesc('c');

        $col->set($msg);
        $col->set($msg2);

        // non-null empty descs

        $msg = new Message('d');
        $msg->setDesc('d');

        $msg2 = new Message('d');
        $msg2->setDesc('');

        $col->set($msg);
        $col->set($msg2);
    }
}
