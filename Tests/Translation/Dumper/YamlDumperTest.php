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

use JMS\TranslationBundle\Exception\InvalidArgumentException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Dumper\YamlDumper;

class YamlDumperTest extends BaseDumperTest
{
    public function testDumpStructureWithoutPrettyPrint()
    {
        $catalogue = new MessageCatalogue();
        $catalogue->setLocale('fr');
        $catalogue->add(new Message('foo.bar.baz'));

        $dumper = new YamlDumper();
        $dumper->setPrettyPrint(false);

        $this->assertEquals($this->getOutput('structure_wo_pretty_print'), $dumper->dump($catalogue, 'messages'));
    }

    protected function getDumper()
    {
        return new YamlDumper();
    }

    protected function getOutput($key)
    {
        if (!is_file($file = __DIR__.'/yml/'.$key.'.yml')) {
            throw new InvalidArgumentException(sprintf('There is no output for key "%s".', $key));
        }

        return file_get_contents($file);
    }
}
