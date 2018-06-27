<?php

/*
 * Copyright 2013 Dieter Peeters <schmittjoh@gmail.com>
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

namespace JMS\TranslationBundle\Tests\Model\Message;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\Message\XliffMessage;
use JMS\TranslationBundle\Tests\Model\MessageTest;

class XliffMessageTest extends MessageTest
{
    public function testSetIsApproved()
    {
        $message = new XliffMessage('foo');
        $this->assertFalse($message->isApproved());
        $this->assertSame($message, $message->setApproved(true));
        $this->assertTrue($message->isApproved());
        $this->assertSame($message, $message->setApproved(false));
        $this->assertFalse($message->isApproved());
    }

    public function testHasState()
    {
        $message = new XliffMessage('foo');
        $this->assertTrue($message->hasState());
        $message->setState(XliffMessage::STATE_TRANSLATED);
        $this->assertTrue($message->hasState());
        $message->setState(XliffMessage::STATE_NONE);
        $this->assertFalse($message->hasState());
        $message->setNew(true);
        $this->assertTrue($message->hasState());
    }

    public function testGetSetState()
    {
        $message = new XliffMessage('foo');
        $this->assertEquals(XliffMessage::STATE_NEW, $message->getState());
        $message->setState(XliffMessage::STATE_TRANSLATED);
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $message->getState());
        $message->setState(XliffMessage::STATE_NONE);
        $this->assertEquals(XliffMessage::STATE_NONE, $message->getState());
        $message->setNew(true);
        $this->assertEquals(XliffMessage::STATE_NEW, $message->getState());
    }

    public function testSetIsNew()
    {
        $message = new XliffMessage('foo');
        $this->assertTrue($message->isNew());
        $this->assertSame($message, $message->setNew(false));
        $this->assertFalse($message->isNew());
        $this->assertSame($message, $message->setNew(true));
        $this->assertTrue($message->isNew());
        $this->assertSame($message, $message->setState(XliffMessage::STATE_NONE));
        $this->assertFalse($message->isNew());
        $this->assertSame($message, $message->setState(XliffMessage::STATE_NEW));
        $this->assertTrue($message->isNew());
        $this->assertSame($message, $message->setState(XliffMessage::STATE_TRANSLATED));
        $this->assertFalse($message->isNew());
    }

    public function testisWritable()
    {
        $message = new XliffMessage('foo');
        $this->assertTrue($message->isWritable());
        $this->assertSame($message, $message->setState(XliffMessage::STATE_FINAL));
        $this->assertFalse($message->isWritable());
        $this->assertSame($message, $message->setState(XliffMessage::STATE_NONE));
        $this->assertTrue($message->isWritable());
        $this->assertSame($message, $message->setState(XliffMessage::STATE_TRANSLATED));
        $this->assertFalse($message->isWritable());
        $this->assertSame($message, $message->setState(XliffMessage::STATE_NEW));
        $this->assertTrue($message->isWritable());
    }

    public function testGetNotes()
    {
        $message = new XliffMessage('foo');
        $this->assertEquals(array(), $message->getNotes());
        $this->assertSame($message, $message->addNote('foo'));
        $this->assertSame(array(array('text' => 'foo')), $message->getNotes());
        $this->assertSame($message, $message->addNote('bar', 'foo'));
        $this->assertSame(array(array('text' => 'foo'), array('text' => 'bar', 'from' => 'foo')), $message->getNotes());
        $this->assertSame($message, $message->setNotes($notes = array(array('text' => 'foo', 'from' => 'bar'))));
        $this->assertSame($notes, $message->getNotes());
    }

    public function testMerge()
    {
        $messageWrite = new XliffMessage('foo');
        $messageWrite->setDesc('foo');
        $messageWrite->setMeaning('foo');
        $messageWrite->addSource($s1 = $this->createMock('JMS\TranslationBundle\Model\SourceInterface'));

        $messageRead = new XliffMessage('foo');
        $messageRead->setDesc('bar');
        $messageRead->setApproved(true);
        $messageRead->setState(XliffMessage::STATE_TRANSLATED);
        $messageRead->addSource($s2 = $this->createMock('JMS\TranslationBundle\Model\SourceInterface'));

        $messageRead2 = new Message('foo');
        $messageRead2->setDesc('bar');
        $messageRead2->addSource($s2);

        $message1 = clone $messageWrite;
        $message2 = clone $messageRead;
        $message1->merge($message2);
        $this->assertEquals('bar', $message1->getDesc());
        $this->assertEquals('foo', $message1->getMeaning());
        $this->assertSame(array($s1, $s2), $message1->getSources());
        $this->assertTrue($message1->isApproved());
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $message1->getState());

        $message3 = clone $messageWrite;
        $message3->setApproved(true);
        $message3->setState(XliffMessage::STATE_TRANSLATED);
        $message4 = clone $messageRead;
        $message4->setApproved(false);
        $message4->setState(XliffMessage::STATE_NONE);
        $message3->merge($message4);
        $this->assertEquals('foo', $message3->getDesc());
        $this->assertTrue($message3->isApproved());
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $message3->getState());

        $message5 = clone $messageWrite;
        $message6 = clone $messageRead2;
        $message5->merge($message6);
        $this->assertEquals('bar', $message5->getDesc());
        $this->assertEquals('foo', $message5->getMeaning());
        $this->assertSame(array($s1, $s2), $message5->getSources());
        $this->assertFalse($message5->isApproved());
        $this->assertEquals(XliffMessage::STATE_NEW, $message5->getState());

        $message7 = clone $messageWrite;
        $message7->setApproved(true);
        $message7->setState(XliffMessage::STATE_TRANSLATED);
        $message8 = clone $messageRead2;
        $message7->merge($message8);
        $this->assertEquals('foo', $message7->getDesc());
        $this->assertTrue($message7->isApproved());
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $message7->getState());
    }

    public function testMergeExisting()
    {
        $scannedMessage = new XliffMessage('foo');
        $scannedMessage->setDesc('foo');

        $existingMessage = new XliffMessage('foo');
        $existingMessage->setLocaleString('bar');
        $existingMessage->addSource(new FileSource('bar'));
        $existingMessage->setApproved(true);
        $existingMessage->setState(XliffMessage::STATE_TRANSLATED);

        $existingMessage2 = new Message('foo');
        $existingMessage2->setLocaleString('bar');
        $existingMessage2->setNew(false);
        $existingMessage2->addSource(new FileSource('bar'));

        $scannedMessage1 = clone $scannedMessage;
        $existingMessage1 = clone $existingMessage;
        $scannedMessage1->mergeExisting($existingMessage1);
        $this->assertEquals('foo', $scannedMessage1->getDesc());
        $this->assertEquals('bar', $scannedMessage1->getLocaleString());
        $this->assertFalse($scannedMessage1->isNew());
        $this->assertEquals(array(), $scannedMessage1->getSources());
        $this->assertFalse($scannedMessage1->isApproved());
        $this->assertEquals(XliffMessage::STATE_NONE, $scannedMessage1->getState());

        $scannedMessage2 = clone $scannedMessage;
        $scannedMessage2->setApproved(true);
        $scannedMessage2->setState(XliffMessage::STATE_TRANSLATED);
        $existingMessage2 = clone $existingMessage;
        $existingMessage2->setDesc('bar');
        $existingMessage2->setApproved(false);
        $existingMessage2->setState(XliffMessage::STATE_NONE);
        $scannedMessage2->mergeExisting($existingMessage2);
        $this->assertEquals('foo', $scannedMessage2->getDesc());
        $this->assertTrue($scannedMessage2->isApproved());
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $scannedMessage2->getState());

        $scannedMessage3 = clone $scannedMessage;
        $existingMessage3 = clone $existingMessage1;
        $scannedMessage3->mergeExisting($existingMessage3);
        $this->assertEquals('foo', $scannedMessage3->getDesc());
        $this->assertEquals('bar', $scannedMessage3->getLocaleString());
        $this->assertFalse($scannedMessage3->isNew());
        $this->assertEquals(array(), $scannedMessage3->getSources());
        $this->assertFalse($scannedMessage3->isApproved());
        $this->assertEquals(XliffMessage::STATE_NONE, $scannedMessage3->getState());

        $scannedMessage4 = clone $scannedMessage;
        $scannedMessage4->setApproved(true);
        $scannedMessage4->setState(XliffMessage::STATE_TRANSLATED);
        $existingMessage4 = clone $existingMessage1;
        $existingMessage4->setDesc('bar');
        $scannedMessage4->mergeExisting($existingMessage4);
        $this->assertEquals('foo', $scannedMessage4->getDesc());
        $this->assertTrue($scannedMessage4->isApproved());
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $scannedMessage4->getState());
    }

    public function testMergeScanned()
    {
        $existingMessage = new XliffMessage('foo');
        $existingMessage->setLocaleString('bar');
        $existingMessage->addSource(new FileSource('bar'));
        $existingMessage->setApproved(false);
        $existingMessage->setState(XliffMessage::STATE_NONE);

        $scannedMessage = new XliffMessage('foo');
        $scannedMessage->setDesc('foo');
        $scannedMessage->setApproved(true);
        $scannedMessage->setState(XliffMessage::STATE_TRANSLATED);

        $scannedMessage1 = new Message('foo');
        $scannedMessage1->setDesc('foo');

        $existingMessage1 = clone $existingMessage;
        $scannedMessage1 = clone $scannedMessage;
        $existingMessage1->mergeScanned($scannedMessage1);
        $this->assertEquals('foo', $existingMessage1->getDesc());
        $this->assertEquals('bar', $existingMessage1->getLocaleString());
        $this->assertFalse($existingMessage1->isNew());
        $this->assertEquals(array(), $existingMessage1->getSources());
        $this->assertTrue($existingMessage1->isApproved());
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $existingMessage1->getState());

        $existingMessage2 = clone $existingMessage;
        $existingMessage2->setDesc('bar');
        $existingMessage2->setApproved(true);
        $existingMessage2->setState(XliffMessage::STATE_TRANSLATED);
        $scannedMessage2 = clone $scannedMessage;
        $scannedMessage2->setDesc('foo');
        $scannedMessage2->setApproved(false);
        $scannedMessage2->setState(XliffMessage::STATE_NONE);
        $existingMessage2->mergeScanned($scannedMessage2);
        $this->assertEquals('bar', $existingMessage2->getDesc());
        $this->assertFalse($existingMessage2->isNew());
        $this->assertEquals(array(), $existingMessage2->getSources());
        $this->assertTrue($existingMessage2->isApproved());
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $existingMessage2->getState());

        $existingMessage3 = clone $existingMessage;
        $scannedMessage3 = clone $scannedMessage1;
        $existingMessage3->mergeScanned($scannedMessage3);
        $this->assertEquals('foo', $existingMessage3->getDesc());
        $this->assertEquals('bar', $existingMessage3->getLocaleString());
        $this->assertFalse($existingMessage3->isNew());
        $this->assertEquals(array(), $existingMessage3->getSources());
        $this->assertTrue($existingMessage3->isApproved());
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $existingMessage3->getState());

        $existingMessage4 = clone $existingMessage;
        $existingMessage4->setDesc('bar');
        $existingMessage4->setApproved(true);
        $existingMessage4->setState(XliffMessage::STATE_TRANSLATED);
        $scannedMessage4 = clone $scannedMessage1;
        $scannedMessage4->setDesc('foo');
        $existingMessage4->mergeScanned($scannedMessage4);
        $this->assertEquals('bar', $existingMessage4->getDesc());
        $this->assertEquals(array(), $existingMessage4->getSources());
        $this->assertTrue($existingMessage4->isApproved());
        $this->assertEquals(XliffMessage::STATE_TRANSLATED, $existingMessage4->getState());
    }
}
