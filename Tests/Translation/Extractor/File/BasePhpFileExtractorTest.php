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

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File;

use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Annotation\Meaning;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use JMS\TranslationBundle\Translation\FileSourceFactory;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

abstract class BasePhpFileExtractorTest extends TestCase
{
    final protected function extract($file, ?FileVisitorInterface $extractor = null)
    {
        $fileRealPath = __DIR__ . '/Fixture/' . $file;
        if (! is_file($fileRealPath)) {
            throw new RuntimeException(sprintf('The file "%s" does not exist.', $fileRealPath));
        }

        if ($extractor === null) {
            $extractor = $this->getDefaultExtractor();
        }

        $lexer = new Lexer();
        if (class_exists(ParserFactory::class)) {
            $factory = new ParserFactory();
            $parser  = $factory->create(ParserFactory::PREFER_PHP7, $lexer);
        } else {
            $parser = new Parser($lexer);
        }

        $ast = $parser->parse(file_get_contents($fileRealPath));

        $catalogue = new MessageCatalogue();
        $extractor->visitPhpFile(new \SplFileInfo($fileRealPath), $catalogue, $ast);

        return $catalogue;
    }

    abstract protected function getDefaultExtractor();

    final protected function getDocParser()
    {
        $docParser = new DocParser();
        $docParser->setImports([
            'desc' => Desc::class,
            'meaning' => Meaning::class,
            'ignore' => Ignore::class,
        ]);
        $docParser->setIgnoreNotImportedAnnotations(true);

        return $docParser;
    }

    protected function getFileSourceFactory()
    {
        return new FileSourceFactory('faux');
    }
}
