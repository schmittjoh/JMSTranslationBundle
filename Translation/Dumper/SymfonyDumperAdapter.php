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

use JMS\TranslationBundle\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\MessageCatalogue as SymfonyCatalogue;
use Symfony\Component\Translation\Dumper\DumperInterface as SymfonyDumper;

use JMS\TranslationBundle\Model\MessageCatalogue;

/**
 * Adapter for Symfony's dumpers.
 *
 * For these dumpers, the same restrictions apply. Namely, using them will
 * cause valuable information to be lost.
 *
 * Also note that only file-based dumpers are compatible.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class SymfonyDumperAdapter implements DumperInterface
{
    private $dumper;
    private $format;

    public function __construct(SymfonyDumper $dumper, $format)
    {
        $this->dumper = $dumper;
        $this->format = $format;
    }

    public function dump(MessageCatalogue $catalogue, $domain = 'messages')
    {
        $symfonyCatalogue = new SymfonyCatalogue($catalogue->getLocale());

        foreach ($catalogue->getDomain($domain)->all() as $id => $message) {
            $symfonyCatalogue->add(
                array($id => $message->getLocaleString()),
                $domain
            );
        }

        $tmpPath = sys_get_temp_dir().'/'.uniqid('translation', false);
        if (!is_dir($tmpPath) && false === @mkdir($tmpPath, 0777, true)) {
            throw new RuntimeException(sprintf('Could not create temporary directory "%s".', $tmpPath));
        }

        $this->dumper->dump($symfonyCatalogue, array(
            'path' => $tmpPath,
        ));

        if (!is_file($tmpFile = $tmpPath.'/'.$domain.'.'.$catalogue->getLocale().'.'.$this->format)) {
            throw new RuntimeException(sprintf('Could not find dumped translation file "%s".', $tmpFile));
        }

        $contents = file_get_contents($tmpFile);
        $fs = new Filesystem();
        $fs->remove($tmpPath);

        if ('' === $contents) {
            throw new RuntimeException(sprintf('Could not dump message catalogue using dumper "%s". It could be that it is not compatible.', get_class($this->dumper)));
        }

        return $contents;
    }
}