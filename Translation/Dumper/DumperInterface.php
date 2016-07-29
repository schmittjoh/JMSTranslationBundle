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

namespace JMS\TranslationBundle\Translation\Dumper;

use JMS\TranslationBundle\Model\MessageCatalogue;

/**
 * Dumper Interface.
 *
 * The interface assumes that one specific domain of the catalogue is dumped
 * at a time.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface DumperInterface
{
    /**
     * Dumps the messages of the given domain.
     *
     * @param MessageCatalogue $catalogue
     * @param string $domain
     * @return string
     */
    public function dump(MessageCatalogue $catalogue, $domain = 'messages');
}
