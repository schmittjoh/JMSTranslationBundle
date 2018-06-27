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
use JMS\TranslationBundle\Tests\BaseTestCase;

class FileSourceTest extends BaseTestCase
{
    public function testGetPath()
    {
        $r = new FileSource('foo');
        $this->assertEquals('foo', $r->getPath());
    }

    public function testGetLine()
    {
        $r = new FileSource('foo');
        $this->assertNull($r->getLine());
    }

    public function testGetLineWhenSet()
    {
        $r = new FileSource('foo', 2);
        $this->assertEquals(2, $r->getLine());
    }

    public function testGetColumn()
    {
        $r = new FileSource('foo');
        $this->assertNull($r->getColumn());
    }

    public function testGetColumnWhenSet()
    {
        $r = new FileSource('foo', 1, 2);
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
            new FileSource('foo'),
            new FileSource('foo'),
            true,
        );

        $tests[] = array(
            new FileSource('foo'),
            new FileSource('bar'),
            false,
        );

        $tests[] = array(
            new FileSource('foo', 1),
            new FileSource('foo', 1),
            true,
        );

        $tests[] = array(
            new FileSource('foo', 1),
            new FileSource('foo', 2),
            false,
        );

        $tests[] = array(
            new FileSource('foo', 1, 2),
            new FileSource('foo', 1, 2),
            true,
        );

        $tests[] = array(
            new FileSource('foo', 1, 2),
            new FileSource('foo', 1, 3),
            false,
        );

        $source = $this->createMock('JMS\TranslationBundle\Model\SourceInterface');
        $source
            ->expects($this->once())
            ->method('equals')
            ->will($this->returnValue(false))
        ;
        $tests[] = array(
            new FileSource('foo'),
            $source,
            false,
        );

        return $tests;
    }

    /**
     * @dataProvider getToStringTests
     */
    public function testToString($r, $expected)
    {
        $this->assertEquals($expected, (string) $r);
    }

    public function getToStringTests()
    {
        $tests = array();

        $tests[] = array(new FileSource('foo/bar'), 'foo/bar');
        $tests[] = array(new FileSource('foo/bar', 1), 'foo/bar on line 1');
        $tests[] = array(new FileSource('foo/bar', null, 2), 'foo/bar');
        $tests[] = array(new FileSource('foo/bar', 1, 2), 'foo/bar on line 1 at column 2');
        $tests[] = array(new FileSource('a/b/c/foo/bar'), 'a/b/c/foo/bar');

        return $tests;
    }
}
