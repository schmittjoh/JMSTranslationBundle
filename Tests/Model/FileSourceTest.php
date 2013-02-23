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
use SplFileInfo;

class FileSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPath()
    {
        $r = new FileSource(new SplFileInfo(__FILE__));
        $this->assertEquals('Tests/Model/FileSourceTest.php', $r->getPath());
    }

    public function testGetLine()
    {
        $r = new FileSource(new SplFileInfo(__FILE__));
        $this->assertNull($r->getLine());
    }

    public function testGetLineWhenSet()
    {
        $r = new FileSource(new SplFileInfo(__FILE__), 2);
        $this->assertEquals(2, $r->getLine());
    }

    public function testGetColumn()
    {
        $r = new FileSource(new SplFileInfo(__FILE__));
        $this->assertNull($r->getColumn());
    }

    public function testGetColumnWhenSet()
    {
        $r = new FileSource(new SplFileInfo('bar'), 1, 2);
        $this->assertEquals(2, $r->getColumn());
    }

    /**
     * @dataProvider getEqualityTests
     */
    public function testEquals($r1, $r2, $expected)
    {
        $this->assertSame($expected, $r1->equals($r2));
        $this->assertSame($expected, $r2->equals($r1));
    }

    public function getEqualityTests()
    {
        $tests = array();

        $tests[] = array(
            new FileSource(new SplFileInfo(__FILE__)),
            new FileSource(new SplFileInfo(__FILE__)),
            true,
        );

        $tests[] = array(
            new FileSource(new SplFileInfo(__FILE__)),
            new FileSource(new SplFileInfo('bar')),
            false,
        );

        $tests[] = array(
            new FileSource(new SplFileInfo(__FILE__), 1),
            new FileSource(new SplFileInfo(__FILE__), 1),
            true,
        );

        $tests[] = array(
            new FileSource(new SplFileInfo(__FILE__), 1),
            new FileSource(new SplFileInfo(__FILE__), 2),
            false,
        );

        $tests[] = array(
            new FileSource(new SplFileInfo('bar'), 1, 2),
            new FileSource(new SplFileInfo('bar'), 1, 2),
            true,
        );

        $tests[] = array(
            new FileSource(new SplFileInfo('bar'), 1, 2),
            new FileSource(new SplFileInfo('bar'), 1, 3),
            false,
        );

        $source = $this->getMock('JMS\TranslationBundle\Model\SourceInterface');
        $source
            ->expects($this->once())
            ->method('equals')
            ->will($this->returnValue(false))
        ;
        $tests[] = array(
            new FileSource(new SplFileInfo(__FILE__)),
            $source,
            false,
        );

        return $tests;
    }
}