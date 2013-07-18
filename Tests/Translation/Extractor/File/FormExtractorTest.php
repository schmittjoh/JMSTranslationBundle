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
    /**
     * @var FormExtractor
     */
    private $extractor;

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

        $message = new Message('form.label.zip', 'address');
        $message->setDesc('ZIP');
        $message->addSource(new FileSource($path, 55));
        $expected->add($message);

        $message = new Message('form.error.password_mismatch', 'validators');
        $message->setDesc('The entered passwords do not match');
        $message->addSource(new FileSource($path, 47));
        $expected->add($message);

        $message = new Message('form.label.created');
        $message->addSource(new FileSource($path, 68));
        $expected->add($message);

        $message = new Message('field.with.placeholder');
        $message->addSource(new FileSource($path, 59));
        $expected->add($message);

        $message = new Message('form.placeholder.text');
        $message->setDesc('Field with a placeholder value');
        $message->addSource(new FileSource($path, 60));
        $expected->add($message);

        $message = new Message('form.placeholder.text.but.no.label');
        $message->setDesc('Field with a placeholder but no label');
        $message->addSource(new FileSource($path, 64));
        $expected->add($message);

        $message = new Message('form.dueDate.empty.year');
        $message->addSource(new FileSource($path, 72));
        $expected->add($message);

        $message = new Message('form.dueDate.empty.month');
        $message->addSource(new FileSource($path, 72));
        $expected->add($message);

        $message = new Message('form.dueDate.empty.day');
        $message->addSource(new FileSource($path, 72));
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

    /**
     * This test is used to check if the default 'translation_domain' option
     * set for the entire form is extracted correctly
     */
    public function testExtractWithDefaultDomain()
    {
        $expected = new MessageCatalogue();
        $path = __DIR__.'/Fixture/MyFormTypeWithDefaultDomain.php';

        $message = new Message('form.label.lastname', 'person');
        $message->setDesc('Lastname');
        $message->addSource(new FileSource($path, 34));
        $expected->add($message);

        $message = new Message('form.label.firstname', 'person');
        $message->addSource(new FileSource($path, 31));
        $expected->add($message);

        $message = new Message('form.label.street', 'address');
        $message->setDesc('Street');
        $message->addSource(new FileSource($path, 37));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyFormTypeWithDefaultDomain.php'));
    }

    /**
     * This test is used to check if translation from subscriber classes and even closures
     * are correctly extracted
     */
    public function testExtractWithWithSubscriberAndListener()
    {
        $expected = new MessageCatalogue();
        $path = __DIR__.'/Fixture/MyFormTypeWithSubscriberAndListener.php';
        $pathSubscriber = __DIR__.'/Fixture/MyFormSubscriber.php';

        $message = new Message('form.label.lastname');
        $message->setDesc('Lastname');
        $message->addSource(new FileSource($path, 36));
        $expected->add($message);

        $message = new Message('form.label.firstname');
        $message->addSource(new FileSource($path, 33));
        $expected->add($message);

        $message = new Message('form.label.password');
        $message->addSource(new FileSource($pathSubscriber, 37));
        $expected->add($message);

        $message = new Message('form.label.password_repeated');
        $message->setDesc('Repeat password');
        $message->addSource(new FileSource($pathSubscriber, 40));
        $expected->add($message);

        $message = new Message('form.label.zip', 'address');
        $message->setDesc('ZIP');
        $message->addSource(new FileSource($path, 51));
        $expected->add($message);

        $message = new Message('form.error.password_mismatch', 'validators');
        $message->setDesc('The entered passwords do not match');
        $message->addSource(new FileSource($pathSubscriber, 42));
        $expected->add($message);

        $catalogue = $this->extract('MyFormTypeWithSubscriberAndListener.php');
        //Merge with the subscriber catalogue
        $catalogue->merge($this->extract('MyFormSubscriber.php'));
        $this->assertEquals($expected, $catalogue);
    }

    /**
     * Run extractor tests with and without a default domain as a form option
     * with the same extractor instance to see that the default domain isn't
     * persisting.
     */
    public function testExtractWithNoDefaultDomainAfterDefaultDomainExtraction()
    {
        $this->testExtractWithDefaultDomain();
        $this->testExtract();
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

        $this->extractor = new FormExtractor($docParser);
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
