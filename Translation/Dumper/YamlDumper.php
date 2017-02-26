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

use JMS\TranslationBundle\Util\Writer;
use JMS\TranslationBundle\Model\Message;
use Symfony\Component\Yaml\Inline;

class YamlDumper extends ArrayStructureDumper
{
    /**
     * @var Writer
     */
    private $writer;

    /**
     * YamlDumper constructor.
     */
    public function __construct()
    {
        $this->writer = new Writer();
    }

    /**
     * @param array $structure
     * @return string
     */
    protected function dumpStructure(array $structure)
    {
        $this->writer->reset();
        $this->dumpStructureRecursively($structure);

        return $this->writer->getContent();
    }

    /**
     * @param array $structure
     */
    private function dumpStructureRecursively(array $structure)
    {
        $isFirst = true;
        $precededByMessage = false;
        foreach ($structure as $k => $v) {
            if ($isMessage = $v instanceof Message) {
                $desc = $v->getDesc();
                $meaning = $v->getMeaning();

                if (!$isFirst && (!$precededByMessage || $desc || $meaning)) {
                    $this->writer->write("\n");
                }

                if ($desc) {
                    $desc = str_replace(array("\r\n", "\n", "\r", "\t"), array('\r\n', '\n', '\r', '\t'), $desc);
                    $this->writer->writeln('# Desc: '.$desc);
                }
                if ($meaning) {
                    $this->writer->writeln('# Meaning: '.$meaning);
                }
            } elseif (!$isFirst) {
                $this->writer->write("\n");
            }

            $isFirst = false;
            $precededByMessage = $isMessage;
            $this->writer->write(Inline::dump($k).':');

            if ($isMessage) {
                $this->writer->write(' '.Inline::dump($v->getLocaleString()));

                if ($v->isNew()) {
                    $this->writer->write('   # FIXME');
                }

                $this->writer->write("\n");

                continue;
            }

            $this->writer
                ->write("\n")
                ->indent();
            $this->dumpStructureRecursively($v);
            $this->writer->outdent();
        }
    }
}
