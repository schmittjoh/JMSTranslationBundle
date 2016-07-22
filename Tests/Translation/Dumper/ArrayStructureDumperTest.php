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

namespace JMS\TranslationBundle\Tests\Translation\Dumper;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;

class ArrayStructureDumperTest extends \PHPUnit_Framework_TestCase
{
    public function testPathWithSubPath()
    {
        $catalogue = new MessageCatalogue();
        $catalogue->setLocale('fr');
        $catalogue->add(new Message('foo.bar'));
        $catalogue->add(new Message('foo.bar.baz'));

        $dumper = $this->getDumper();
        $dumper
            ->expects($this->once())
            ->method('dumpStructure')
            ->with(array(
                'foo' => array(
                    'bar' => new Message('foo.bar'),
                    'bar.baz' => new Message('foo.bar.baz'),
                ),
            ))
            ->will($this->returnValue('foo'))
        ;

        $this->assertEquals('foo', $dumper->dump($catalogue, 'messages'));
    }

    private function getDumper()
    {
        return $this->getMockForAbstractClass('JMS\TranslationBundle\Translation\Dumper\ArrayStructureDumper');
    }
}
