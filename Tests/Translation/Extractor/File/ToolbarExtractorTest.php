<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 * 
 * Added by Nicky Gerritsen in april 2015 for the StreamOne manager
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

use JMS\TranslationBundle\Translation\Extractor\File\ToolbarExtractor;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;

class ToolbarExtractorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ToolbarExtractor
     */
    private $extractor;

    public function testExtract()
    {
        $expected = new MessageCatalogue();
        $path = __DIR__.'/Fixture/DummyToolbarDefinition.php';

        $message = new Message('dummy.xyz');
        $message->addSource(new FileSource($path, 16));
        $expected->add($message);

        $message = new Message('dummy.abc');
        $message->setDesc('This is abc');
        $message->addSource(new FileSource($path, 19));
        $expected->add($message);

        $message = new Message('dummy.def');
        $message->setDesc('This is a test');
        $message->addSource(new FileSource($path, 25));
        $expected->add($message);

        $message = new Message('dummy.ghi');
        $message->setDesc('Ghi');
        $message->addSource(new FileSource($path, 31));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('DummyToolbarDefinition.php'));
    }

    protected function setUp()
    {
        $docParser = new DocParser();
        $docParser->setImports(array(
            'desc' => 'JMS\TranslationBundle\Annotation\Desc',
            'meaning' => 'JMS\TranslationBundle\Annotation\Meaning',
            'ignore' => 'JMS\TranslationBundle\Annotation\Ignore',
        ));
        $docParser->setIgnoreNotImportedAnnotations(true);

        $this->extractor = new ToolbarExtractor($docParser);
    }

    private function extract($file)
    {
        if (!is_file($file = __DIR__.'/Fixture/'.$file)) {
            throw new RuntimeException(sprintf('The file "%s" does not exist.', $file));
        }
        $file = new \SplFileInfo($file);

        $lexer = new \PHPParser_Lexer(file_get_contents($file));
        $parser = new \PHPParser_Parser();
        $ast = $parser->parse($lexer);

        $catalogue = new MessageCatalogue();
        $this->extractor->visitPhpFile($file, $catalogue, $ast);

        return $catalogue;
    }
}
