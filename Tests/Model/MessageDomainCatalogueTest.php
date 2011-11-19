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
use JMS\TranslationBundle\Model\MessageDomainCatalogue;

class MessageDomainCatalogueTest extends \PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $domain = new MessageDomainCatalogue('messages', 'fr');
        $domain->add($m = new Message('foo'));

        $this->assertSame(array('foo' => $m), $domain->all());
    }

    public function testGet()
    {
        $domain = new MessageDomainCatalogue('messages', 'fr');
        $domain->add($message = Message::create('foo'));

        $this->assertTrue($domain->has('foo'));
        $this->assertSame($message, $domain->get('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetThrowsExceptionWhenMessageDoesNotExist()
    {
        $catalogue = new MessageDomainCatalogue('messages', 'fr');
        $catalogue->get('foo');
    }
}