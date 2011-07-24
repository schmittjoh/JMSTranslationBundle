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

namespace JMS\TranslationBundle\Translation\Comparison;

use JMS\TranslationBundle\Model\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Compares two message catalogues.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class CatalogueComparator
{
    private $ignoredDomains = array();

    public function setIgnoredDomains(array $domains)
    {
        $this->ignoredDomains = $domains;
    }

    /**
     * Compares two message catalogues.
     *
     * @param MessageCatalogueInterface $a
     * @param MessageCatalogueInterface $b
     * @throws \RuntimeException
     * @return \JMS\CommandBundle\Translation\ComparisonResult
     */
    public function compare(MessageCatalogueInterface $current, MessageCatalogue $new)
    {
        $newMessages = array();
        $modifiedMessages = array();

        foreach ($new->all() as $id => $message) {
            if (isset($this->ignoredDomains[$message->getDomain()])) {
                continue;
            }

            if ($current->has($id)) {
                // FIXME: Compare what has changed

                continue;
            }

            $newMessages[$id] = $message;
        }

        $deletedMessages = array();
        foreach ($current->all() as $domain => $messages) {
            if (isset($this->ignoredDomains[$domain])) {
                continue;
            }

            foreach ($messages as $id => $message) {
                if ($new->has($id)) {
                    continue;
                }

                $deletedMessages[$id] = array($domain, $message);
            }
        }

        return new ChangeSet($newMessages, $deletedMessages);
    }
}
