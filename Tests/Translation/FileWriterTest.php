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

namespace JMS\TranslationBundle\Tests\Translation;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Dumper\DumperInterface;
use JMS\TranslationBundle\Translation\FileWriter;
use PHPUnit\Framework\TestCase;

class FileWriterTest extends TestCase
{
    public function testCatalogueIsSortedBeforeBeingDumped()
    {
        $dumper = $this->createMock(DumperInterface::class);

        $self = $this;
        $dumper
            ->expects($this->once())
            ->method('dump')
            ->willReturnCallback(static function ($v) use ($self) {
                $self->assertEquals(['foo.bar', 'foo.bar.baz'], array_keys($v->getDomain('messages')->all()));
            });

        $writer = new FileWriter(['test' => $dumper]);

        $catalogue = new MessageCatalogue();
        $catalogue->setLocale('fr');
        $catalogue->add(new Message('foo.bar.baz'));
        $catalogue->add(new Message('foo.bar'));

        $path = tempnam(sys_get_temp_dir(), 'filewriter');
        $writer->write($catalogue, 'messages', $path, 'test');
        @unlink($path);
    }
}
