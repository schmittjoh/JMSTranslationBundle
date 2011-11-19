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

namespace JMS\TranslationBundle\Translation\Dumper;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;

abstract class ArrayStructureDumper implements DumperInterface
{
    private $prettyPrint = true;

    public function setPrettyPrint($bool)
    {
        $this->prettyPrint = (Boolean) $bool;
    }

    public function dump(MessageCatalogue $catalogue, $domain = 'messages')
    {
        $structure = $catalogue->getDomain($domain)->all();

        if ($this->prettyPrint) {
            $tmpStructure = array();

            foreach ($structure as $id => $message) {
                $pointer = &$tmpStructure;
                $parts = explode('.', $id);

                // this algorithm only works if the messages are alphabetically
                // ordered, in particular it must be guaranteed that parent paths
                // are before sub-paths, e.g.
                // array_keys($structure) = array('foo.bar', 'foo.bar.baz')
                // but NOT: array_keys($structure) = array('foo.bar.baz', 'foo.bar')
                for ($i=0,$c=count($parts); $i<$c; $i++) {
                    if ($i+1 === $c) {
                        $pointer[$parts[$i]] = $message;
                        break;
                    }

                    if (!isset($pointer[$parts[$i]])) {
                        $pointer[$parts[$i]] = array();
                    }

                    if ($pointer[$parts[$i]] instanceof Message) {
                        $subPath = implode('.', array_slice($parts, $i));
                        $pointer[$subPath] = $message;
                        break;
                    }

                    $pointer = &$pointer[$parts[$i]];
                }
            }

            $structure = $tmpStructure;
            unset($tmpStructure);
        }

        return $this->dumpStructure($structure);
    }

    abstract protected function dumpStructure(array $structure);
}