<?php

declare(strict_types=1);

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

namespace JMS\TranslationBundle\Translation\Comparison;

use JMS\TranslationBundle\Model\Message;

class ChangeSet
{
    public function __construct(
        /** @var Message[] */
        private readonly array $addedMessages,
        /** @var Message[] */
        private readonly array $deletedMessages,
    ) {
    }

    /**
     * @return Message[]
     */
    public function getAddedMessages(): array
    {
        return $this->addedMessages;
    }

    /**
     * @return Message[]
     */
    public function getDeletedMessages(): array
    {
        return $this->deletedMessages;
    }
}
