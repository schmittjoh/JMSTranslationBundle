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

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File;

use JMS\TranslationBundle\Exception\RuntimeException;
use Doctrine\Common\Annotations\AnnotationReader;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use JMS\TranslationBundle\Translation\Extractor\File\ValidationExtractor;
use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Translation\Extractor\File\FormExtractor;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;

class ValidationExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractConstraints()
    {
        $expected = new MessageCatalogue();
        $path = __DIR__.'/Fixture/MyFormModel.php';

        $message = new Message('form.error.name_required', 'validators');
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyFormModel.php'));
    }

    private function extract($file, ValidationExtractor $extractor = null)
    {
        if (!is_file($file = __DIR__.'/Fixture/'.$file)) {
            throw new RuntimeException(sprintf('The file "%s" does not exist.', $file));
        }
        $file = new \SplFileInfo($file);

        //use correct factory class depending on whether using Symfony 2 or 3
        if (class_exists('Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory')) {
            $metadataFactoryClass = 'Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory';
        } else {
            $metadataFactoryClass = 'Symfony\Component\Validator\Mapping\ClassMetadataFactory';
        }

        if (null === $extractor) {
            $factory = new $metadataFactoryClass(new AnnotationLoader(new AnnotationReader()));
            $extractor = new ValidationExtractor($factory);
        }

        $lexer = new Lexer();
        if (class_exists('PhpParser\ParserFactory')) {
            $factory = new ParserFactory();
            $parser = $factory->create(ParserFactory::PREFER_PHP7, $lexer);
        } else {
            $parser = new Parser($lexer);
        }

        $ast = $parser->parse(file_get_contents($file));

        $catalogue = new MessageCatalogue();
        $extractor->visitPhpFile($file, $catalogue, $ast);

        return $catalogue;
    }
}
