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

namespace JMS\TranslationBundle\Translation;

use JMS\TranslationBundle\Model\Message;

/**
 * Translation Container Interface.
 *
 * This interface may be implemented by classes if they contain translations
 * that need to be dynamically generated.
 *
 * A good example when to use this interface is if you have a choice field,
 * and you want to make your choices available for translation.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface TranslationContainerInterface
{
    /**
     * Returns an array of messages.
     *
     * @return array<Message>
     */
    public static function getTranslationMessages();
}
