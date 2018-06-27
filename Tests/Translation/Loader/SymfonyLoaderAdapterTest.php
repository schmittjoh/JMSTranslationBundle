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

namespace JMS\TranslationBundle\Tests\Translation\Loader;

use JMS\TranslationBundle\Tests\BaseTestCase;
use JMS\TranslationBundle\Translation\Loader\SymfonyLoaderAdapter;
use Symfony\Component\Translation\MessageCatalogue;

class SymfonyLoaderAdapterTest extends BaseTestCase
{
    public function testLoad()
    {
        $symfonyCatalogue = new MessageCatalogue('en');
        $symfonyCatalogue->add(array('foo' => 'bar'));
        
        $symfonyLoader = $this->createMock('Symfony\Component\Translation\Loader\LoaderInterface');
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
