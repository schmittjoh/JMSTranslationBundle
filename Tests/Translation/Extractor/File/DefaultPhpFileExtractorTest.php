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

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\File\DefaultPhpFileExtractor;

class DefaultPhpFileExtractorTest extends BasePhpFileExtractorTest
{
    public function testExtractController()
    {
        $catalogue = $this->extract('Controller.php');

        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/Controller.php');

        $expected = new MessageCatalogue();

        $message = new Message('text.foo_bar');
        $message->setDesc('Foo bar');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 45));
        $expected->add($message);

        $message = new Message('text.sign_up_successful');
        $message->setDesc('Welcome %name%! Thanks for signing up.');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 52));
        $expected->add($message);

        $message = new Message('button.archive');
        $message->setDesc('Archive Message');
        $message->setMeaning('The verb (to archive), describes an action');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 59));
        $expected->add($message);

        $message = new Message('text.irrelevant_doc_comment', 'baz');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 71));
        $expected->add($message);

        $message = new Message('text.array_method_call');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 76));
        $expected->add($message);

        $message = new Message('text.var.assign');
        $message->setDesc('The var %foo% should be assigned.');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 82));
        $expected->add($message);

        $this->assertEquals($expected, $catalogue);
    }

    public function testExtractTemplate()
    {
        $expected = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/template.html.php');

        $message = new Message('foo.bar');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 1));
        $expected->add($message);

        $message = new Message('baz', 'moo');
        $message->setDesc('Foo Bar');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 3));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('template.html.php'));
    }

    protected function getDefaultExtractor()
    {
        return new DefaultPhpFileExtractor($this->getDocParser(), $fileSourceFactory = $this->getFileSourceFactory());
    }
}
