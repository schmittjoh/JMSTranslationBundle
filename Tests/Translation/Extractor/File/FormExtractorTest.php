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

use JMS\TranslationBundle\Translation\Extractor\File\FormExtractor;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use Symfony\Component\HttpKernel\Kernel;

class FormExtractorTest extends BasePhpFileExtractorTest
{
    /**
     * @group placeholder
     */
    public function testPlaceholderExtract()
    {
        $expected = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/MyPlaceholderFormType.php');

        $message = new Message('field.with.placeholder');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 29));
        $expected->add($message);

        $message = new Message('form.placeholder.text');
        $message->setDesc('Field with a placeholder value');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 30));
        $expected->add($message);

        $message = new Message('form.placeholder.text.but.no.label');
        $message->setDesc('Field with a placeholder but no label');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 34));
        $expected->add($message);

        $message = new Message('form.choice_placeholder');
        $message->setDesc('Choice field with a placeholder');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 37));
        $expected->add($message);

        $message = new Message('form.choice_empty_value');
        $message->setDesc('Choice field with an empty_value');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 40));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyPlaceholderFormType.php'));
    }

    /**
     * @group testExtract
     */
    public function testExtract()
    {
        $expected = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/MyFormType.php');

        // Symfony >= 3.0 switch the default behavior of the choice field following a BC break introduced in 2.7
        // @see https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#choices_as_values
        if (Kernel::VERSION_ID >= 30000) {
            $message = new Message('foo');
        } else {
            $message = new Message('bar');
        }
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 36));
        $expected->add($message);

        $message = new Message('form.states.empty_value');
        $message->setDesc('Please select a state');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 37));
        $expected->add($message);

        $message = new Message('form.label.lastname');
        $message->setDesc('Lastname');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 33));
        $expected->add($message);

        $message = new Message('form.label.firstname');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 30));
        $expected->add($message);

        $message = new Message('form.label.password');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 42));
        $expected->add($message);

        $message = new Message('form.label.password_repeated');
        $message->setDesc('Repeat password');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 45));
        $expected->add($message);

        $message = new Message('form.label.street', 'address');
        $message->setDesc('Street');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 50));
        $expected->add($message);

        $message = new Message('form.label.zip', 'address');
        $message->setDesc('ZIP');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 55));
        $expected->add($message);

        $message = new Message('form.error.password_mismatch', 'validators');
        $message->setDesc('The entered passwords do not match');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 47));
        $expected->add($message);

        $message = new Message('form.label.created');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 75));
        $expected->add($message);

        $message = new Message('field.with.placeholder');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 59));
        $expected->add($message);

        $message = new Message('form.placeholder.text');
        $message->setDesc('Field with a placeholder value');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 60));
        $expected->add($message);

        $message = new Message('form.placeholder.text.but.no.label');
        $message->setDesc('Field with a placeholder but no label');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 64));
        $expected->add($message);

        $message = new Message('form.dueDate.empty.year');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 79));
        $expected->add($message);

        $message = new Message('form.dueDate.empty.month');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 79));
        $expected->add($message);

        $message = new Message('form.dueDate.empty.day');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 79));
        $expected->add($message);

        $message = new Message('form.choice.choice_as_values.label.foo');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 68));
        $expected->add($message);

        $message = new Message('form.choice.choice_as_values.label.bar');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 69));
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
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/MyFormTypeWithInterface.php');

        // Symfony >= 3.0 switch the default behavior of the choice field following a BC break introduced in 2.7
        // @see https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md#choices_as_values
        if (Kernel::VERSION_ID >= 30000) {
            $message = new Message('foo');
        } else {
            $message = new Message('bar');
        }
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 36));
        $expected->add($message);

        $message = new Message('form.states.empty_value');
        $message->setDesc('Please select a state');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 37));
        $expected->add($message);

        $message = new Message('form.label.lastname');
        $message->setDesc('Lastname');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 33));
        $expected->add($message);

        $message = new Message('form.label.firstname');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 30));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyFormTypeWithInterface.php'));
    }

    protected function getDefaultDomainFixture($fixtureFile)
    {
        $expected = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo($fixtureFile);

        $message = new Message('form.label.lastname', 'person');
        $message->setDesc('Lastname');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 34));
        $expected->add($message);

        $message = new Message('form.label.firstname', 'person');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 31));
        $expected->add($message);

        $message = new Message('form.label.street', 'address');
        $message->setDesc('Street');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 37));
        $expected->add($message);

        return $expected;
    }

    /**
     * This test is used to check if the default 'translation_domain' option
     * set for the entire form is extracted correctly
     */
    public function testExtractWithDefaultDomain()
    {
        $this->assertEquals($this->getDefaultDomainFixture(__DIR__.'/Fixture/MyFormTypeWithDefaultDomain.php'), $this->extract('MyFormTypeWithDefaultDomain.php'));
    }

    /**
     * This test is used to check if the default 'translation_domain' option
     * set for the entire form is extracted correctly when set via setDefault
     */
    public function testExtractWithDefaultDomainSetDefault()
    {
        $this->assertEquals($this->getDefaultDomainFixture(__DIR__.'/Fixture/MyFormTypeWithDefaultDomainSetDefault.php'), $this->extract('MyFormTypeWithDefaultDomainSetDefault.php'));
    }

    /**
     * This test is used to check if translation from subscriber classes and even closures
     * are correctly extracted
     */
    public function testExtractWithWithSubscriberAndListener()
    {
        $expected = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/MyFormTypeWithSubscriberAndListener.php');
        $subscriberFixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/MyFormSubscriber.php');

        $message = new Message('form.label.lastname');
        $message->setDesc('Lastname');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 36));
        $expected->add($message);

        $message = new Message('form.label.firstname');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 33));
        $expected->add($message);

        $message = new Message('form.label.password');
        $message->addSource($fileSourceFactory->create($subscriberFixtureSplInfo, 37));
        $expected->add($message);

        $message = new Message('form.label.password_repeated');
        $message->setDesc('Repeat password');
        $message->addSource($fileSourceFactory->create($subscriberFixtureSplInfo, 40));
        $expected->add($message);

        $message = new Message('form.label.zip', 'address');
        $message->setDesc('ZIP');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 51));
        $expected->add($message);

        $message = new Message('form.error.password_mismatch', 'validators');
        $message->setDesc('The entered passwords do not match');
        $message->addSource($fileSourceFactory->create($subscriberFixtureSplInfo, 42));
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

    public function testAttrArrayForm()
    {
        $expected = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/MyAttrArrayType.php');

        $message = new Message('form.label.firstname');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 31));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyAttrArrayType.php'));

    }

    protected function getDefaultExtractor()
    {
        return new FormExtractor($this->getDocParser(), $this->getFileSourceFactory());
    }
}
