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

use JMS\TranslationBundle\Translation\Extractor\File\FormExtractor;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;

class FormExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtract()
    {
        $expected = new MessageCatalogue();
        $path = __DIR__.'/Fixture/MyFormType.php';
        
        $message = new Message('bar');
        $message->addSource(new FileSource($path, 36));
        $expected->add($message);

        $message = new Message('form.states.empty_value');
        $message->setDesc('Please select a state');
        $message->addSource(new FileSource($path, 37));
        $expected->add($message);

        $message = new Message('form.label.lastname');
        $message->setDesc('Lastname');
        $message->addSource(new FileSource($path, 33));
        $expected->add($message);

        $message = new Message('form.label.firstname');
        $message->addSource(new FileSource($path, 30));
        $expected->add($message);

        $message = new Message('form.label.password');
        $message->addSource(new FileSource($path, 42));
        $expected->add($message);

        $message = new Message('form.label.password_repeated');
        $message->setDesc('Repeat password');
        $message->addSource(new FileSource($path, 45));
        $expected->add($message);

        $message = new Message('form.label.street', 'address');
        $message->setDesc('Street');
        $message->addSource(new FileSource($path, 50));
        $expected->add($message);

        $message = new Message('form.error.password_mismatch', 'validators');
        $message->setDesc('The entered passwords do not match');
        $message->addSource(new FileSource($path, 47));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyFormType.php'));
    }

    /**
     * This test is used to check compatibility with Symfony 2.1
     * In Symfony 2.1 the AbstractType must use FormBuilderInterface instead of FormBuilder
     */
    public function testExtractWithInterface()
    {
        $expected = new MessageCatalogue();
        $path = __DIR__.'/Fixture/MyFormTypeWithInterface.php';
        
        $message = new Message('bar');
        $message->addSource(new FileSource($path, 36));
        $expected->add($message);

        $message = new Message('form.states.empty_value');
        $message->setDesc('Please select a state');
        $message->addSource(new FileSource($path, 37));
        $expected->add($message);

        $message = new Message('form.label.lastname');
        $message->setDesc('Lastname');
        $message->addSource(new FileSource($path, 33));
        $expected->add($message);

        $message = new Message('form.label.firstname');
        $message->addSource(new FileSource($path, 30));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyFormTypeWithInterface.php'));
    }

    private function extract($file, FormExtractor $extractor = null)
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

            $extractor = new FormExtractor($docParser);
        }

        $lexer = new \PHPParser_Lexer(file_get_contents($file));
        $parser = new \PHPParser_Parser();
        $ast = $parser->parse($lexer);

        $catalogue = new MessageCatalogue();
        $extractor->visitPhpFile($file, $catalogue, $ast);

        return $catalogue;
    }
}
