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

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\File\ValidationExtractor;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

class ValidationExtractorTest extends TestCase
{
    public function testExtractConstraints()
    {
        $expected = new MessageCatalogue();
        $path     = __DIR__ . '/Fixture/MyFormModel.php';

        $message = new Message('form.error.name_required', 'validators');
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyFormModel.php'));
    }

    private function extract($file, ?ValidationExtractor $extractor = null)
    {
        $fileRealPath = __DIR__ . '/Fixture/' . $file;
        if (! is_file($fileRealPath)) {
            throw new RuntimeException(sprintf('The file "%s" does not exist.', $fileRealPath));
        }

        $metadataFactoryClass = LazyLoadingMetadataFactory::class;

        if ($extractor === null) {
            $factory   = new $metadataFactoryClass(new AnnotationLoader(new AnnotationReader()));
            $extractor = new ValidationExtractor($factory);
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
}
