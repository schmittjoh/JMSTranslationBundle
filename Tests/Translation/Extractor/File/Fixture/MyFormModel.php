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

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File\Fixture;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MyFormModel implements TranslationContainerInterface
{
    private static $choices = array(
        'foo' => 'form.label.choice.foo',
        'bar' => 'form.label.choice.bar',
    );

    /**
     * @Assert\NotBlank(message = "form.error.name_required")
     */
    private $name;

    public static function getTranslationMessages()
    {
        $messages = array();

        foreach (self::$choices as $trans) {
            $message = new Message($trans);
            $message->addSource(new FileSource(__FILE__, 13));
            $messages[] = $message;
        }

        return $messages;
    }
}
