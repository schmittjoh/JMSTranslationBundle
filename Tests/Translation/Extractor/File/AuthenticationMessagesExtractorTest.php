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
use JMS\TranslationBundle\Translation\Extractor\File\AuthenticationMessagesExtractor;

class AuthenticationMessagesExtractorTest extends BasePhpFileExtractorTest
{
    public function testExtract()
    {
        $expected = new MessageCatalogue();

        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/MyAuthException.php');

        $message = new Message('security.authentication_error.foo', 'authentication');
        $message->setDesc('%foo% is invalid.');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 31));
        $expected->add($message);

        $message = new Message('security.authentication_error.bar', 'authentication');
        $message->setDesc('An authentication error occurred.');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 35));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('MyAuthException.php'));
    }

    protected function getDefaultExtractor()
    {
        return new AuthenticationMessagesExtractor($this->getDocParser(), $this->getFileSourceFactory());
    }
}
