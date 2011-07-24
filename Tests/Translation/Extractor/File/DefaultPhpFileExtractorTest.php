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
use Doctrine\Common\Annotations\DocParser;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\File\DefaultPhpFileExtractor;

class DefaultPhpFileExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractController()
    {
        $catalogue = $this->extract('Controller.php');

        $path = __DIR__.'/Fixture/Controller.php';

        $expected = new MessageCatalogue();

        $message = new Message('text.foo_bar');
        $message->setDesc('Foo bar');
        $message->addSource(new FileSource($path, 45));
        $expected->add($message);

        $message = new Message('text.sign_up_successful');
        $message->setDesc('Welcome %name%! Thanks for signing up.');
        $message->addSource(new FileSource($path, 52));
        $expected->add($message);

        $message = new Message('button.archive');
        $message->setDesc('Archive Message');
        $message->setMeaning('The verb (to archive), describes an action');
        $message->addSource(new FileSource($path, 59));
        $expected->add($message);

        $message = new Message('text.irrelevant_doc_comment', 'baz');
        $message->addSource(new FileSource($path, 71));
        $expected->add($message);

        $message = new Message('text.array_method_call');
        $message->addSource(new FileSource($path, 76));
        $expected->add($message);

        $this->assertEquals($expected, $catalogue);
    }

    public function testExtractTemplate()
    {
        $expected = new MessageCatalogue();
        $path = __DIR__.'/Fixture/template.html.php';

        $message = new Message('foo.bar');
        $message->addSource(new FileSource($path, 1));
        $expected->add($message);

        $message = new Message('baz', 'moo');
        $message->setDesc('Foo Bar');
        $message->addSource(new FileSource($path, 3));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('template.html.php'));
    }

    private function extract($file, DefaultPhpFileExtractor $extractor = null)
    {
        if (!is_file($file = __DIR__.'/Fixture/'.$file)) {
            throw new RuntimeException(sprintf('The file "%s" does not exist.', $file));
        }
        $file = new \SplFileInfo($file);

        if (null === $extractor) {
            $docParser = new DocParser();
            $docParser->setImports(array(
                'desc' => 'JMS\TranslationBundle\Annotation\Desc',
                'meaning' => 'JMS\TranslationBundle\Annotation\Meaning',
                'ignore' => 'JMS\TranslationBundle\Annotation\Ignore',
            ));
            $docParser->setIgnoreNotImportedAnnotations(true);

            $extractor = new DefaultPhpFileExtractor($docParser);
        }

        $lexer = new \PHPParser_Lexer(file_get_contents($file));
        $parser = new \PHPParser_Parser();
        $ast = $parser->parse($lexer);

        $catalogue = new MessageCatalogue();
        $extractor->visitPhpFile($file, $catalogue, $ast);

        return $catalogue;
    }
}