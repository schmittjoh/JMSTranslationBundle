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

namespace JMS\TranslationBundle\Tests\Translation\Comparison;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Comparison\CatalogueComparator;
use JMS\TranslationBundle\Translation\Comparison\ChangeSet;
use PHPUnit\Framework\TestCase;

class CatalogueComparatorTest extends TestCase
{
    public function testCompareWithMultipleDomains()
    {
        $current = new MessageCatalogue();
        $current->add(Message::create('foo')->setLocaleString('bar'));
        $current->add(Message::create('bar', 'routes')->setLocaleString('baz'));

        $new = new MessageCatalogue();
        $new->add(new Message('foo'));
        $new->add(new Message('bar'));

        $expected   = new ChangeSet(
            [new Message('bar')],
            [Message::create('bar', 'routes')->setLocaleString('baz')]
        );
        $comparator = new CatalogueComparator();

        $this->assertEquals($expected, $comparator->compare($current, $new));
    }
}
