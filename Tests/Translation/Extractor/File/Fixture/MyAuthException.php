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

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class MyAuthException extends AuthenticationException
{
    private $foo;

    public function getMessageKey()
    {
        if (!empty($this->foo)) {
            /** @Desc("%foo% is invalid.") */
            return 'security.authentication_error.foo';
        }

        /** @Desc("An authentication error occurred.") */
        return 'security.authentication_error.bar';
    }

    public function getMessageParameters()
    {
        return array('foo' => $foo);
    }
}
