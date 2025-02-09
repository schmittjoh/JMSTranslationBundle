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
use JMS\TranslationBundle\Model\SourceInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileSourceTest extends TestCase
{
    public function testGetPath(): void
    {
        $r = new FileSource('foo');
        $this->assertEquals('foo', $r->getPath());
    }

    public function testGetLine(): void
    {
        $r = new FileSource('foo');
        $this->assertNull($r->getLine());
    }

    public function testGetLineWhenSet(): void
    {
        $r = new FileSource('foo', 2);
        $this->assertEquals(2, $r->getLine());
    }

    public function testGetColumn(): void
    {
        $r = new FileSource('foo');
        $this->assertNull($r->getColumn());
    }

    public function testGetColumnWhenSet(): void
    {
        $r = new FileSource('foo', 1, 2);
        $this->assertEquals(2, $r->getColumn());
    }

    #[DataProvider('getEqualityTests')]
    public function testEquals($r1, $r2, $expected): void
    {
        $this->assertSame($expected, $r1->equals($r2));
        $this->assertSame($expected, $r2->equals($r1));
    }

    public static function getEqualityTests(): array
    {
        $tests = [];

        $tests[] = [
            new FileSource('foo'),
            new FileSource('foo'),
            true,
        ];

        $tests[] = [
            new FileSource('foo'),
            new FileSource('bar'),
            false,
        ];

        $tests[] = [
            new FileSource('foo', 1),
            new FileSource('foo', 1),
            true,
        ];

        $tests[] = [
            new FileSource('foo', 1),
            new FileSource('foo', 2),
            false,
        ];

        $tests[] = [
            new FileSource('foo', 1, 2),
            new FileSource('foo', 1, 2),
            true,
        ];

        $tests[] = [
            new FileSource('foo', 1, 2),
            new FileSource('foo', 1, 3),
            false,
        ];

        $tests[] = [
            new FileSource('foo'),
            new class implements SourceInterface {
                public function equals(SourceInterface $source): bool
                {
                    return false;
                }

                public function __toString(): string
                {
                    return '';
                }
            },
            false,
        ];

        return $tests;
    }

    #[DataProvider('getToStringTests')]
    public function testToString(FileSource $r, string $expected): void
    {
        $this->assertEquals($expected, (string) $r);
    }

    public static function getToStringTests(): array
    {
        $tests = [];

        $tests[] = [new FileSource('foo/bar'), 'foo/bar'];
        $tests[] = [new FileSource('foo/bar', 1), 'foo/bar on line 1'];
        $tests[] = [new FileSource('foo/bar', null, 2), 'foo/bar'];
        $tests[] = [new FileSource('foo/bar', 1, 2), 'foo/bar on line 1 at column 2'];
        $tests[] = [new FileSource('a/b/c/foo/bar'), 'a/b/c/foo/bar'];

        return $tests;
    }
}
