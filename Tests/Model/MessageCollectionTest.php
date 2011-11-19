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
use JMS\TranslationBundle\Model\MessageCollection;

class MessageCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $domain = new MessageCollection();
        $domain->add($m = new Message('foo'));

        $this->assertSame(array('foo' => $m), $domain->all());
    }

    public function testAddMerges()
    {
        $m2 = $this->getMockBuilder('JMS\TranslationBundle\Model\Message')
            ->disableOriginalConstructor()
            ->getMock();

        $m1 = $this->getMockBuilder('JMS\TranslationBundle\Model\Message')
            ->disableOriginalConstructor()
            ->getMock();
        $m1->expects($this->once())
            ->method('merge')
            ->with($m2);

        $col = new MessageCollection();
        $col->add($m1);
        $col->add($m2);
    }

    public function testGet()
    {
        $domain = new MessageCollection();
        $domain->add($message = Message::create('foo'));

        $this->assertTrue($domain->has('foo'));
        $this->assertSame($message, $domain->get('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetThrowsExceptionWhenMessageDoesNotExist()
    {
        $catalogue = new MessageCollection();
        $catalogue->get('foo');
    }

    public function testSet()
    {
        $col = new MessageCollection();
        $col->add($m = Message::create('foo'));

        $this->assertTrue($col->has('foo'));
        $this->assertSame($m, $col->get('foo'));
    }

    public function testSetDoesNotMerge()
    {
        $m2 = $this->getMockBuilder('JMS\TranslationBundle\Model\Message')
            ->disableOriginalConstructor()
            ->getMock();
        $m2->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('foo'));

        $m1 = $this->getMockBuilder('JMS\TranslationBundle\Model\Message')
            ->disableOriginalConstructor()
            ->getMock();
        $m1->expects($this->never())
            ->method('merge');
        $m1->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('foo'));

        $col = new MessageCollection();
        $col->set($m1);
        $col->set($m2);

        $this->assertSame($m2, $col->get('foo'));
    }

    public function testSort()
    {
        $col = new MessageCollection();
        $col->add(new Message('b'));
        $col->add(new Message('c'));
        $col->add(new Message('a'));

        $this->assertEquals(array('b', 'c', 'a'), array_keys($col->all()));

        $col->sort('strcasecmp');
        $this->assertEquals(array('a', 'b', 'c'), array_keys($col->all()));
    }

    public function testFilter()
    {
        $col = new MessageCollection();
        $col->add($m = new Message('a'));
        $col->add(new Message('b'));
        $col->add(new Message('c'));
        $col->filter(function($v) { return 'a' === $v->getId(); });

        $this->assertEquals(array('a'), array_keys($col->all()));
        $this->assertSame($m, $col->get('a'));
    }

    public function testMerge()
    {
        $col = new MessageCollection();
        $col->add(new Message('a'));

        $col2 = new MessageCollection();
        $col2->add(new Message('b'));

        $col->merge($col2);
        $this->assertEquals(array('a', 'b'), array_keys($col->all()));
    }
}