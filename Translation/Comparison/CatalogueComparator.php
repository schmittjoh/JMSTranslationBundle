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

/**
 * Compares two message catalogues.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class CatalogueComparator
{
    private $domains = array();
    private $ignoredDomains = array();

    public function setDomains(array $domains)
    {
        $this->domains = $domains;
    }

    /**
     * @param array $domains
     */
    public function setIgnoredDomains(array $domains)
    {
        $this->ignoredDomains = $domains;
    }

    /**
     * Compares two message catalogues.
     *
     * @param MessageCatalogue $current
     * @param MessageCatalogue $new
     * @return ChangeSet
     */
    public function compare(MessageCatalogue $current, MessageCatalogue $new)
    {
        $newMessages = array();

        foreach ($new->getDomains() as $name => $domain) {
            if ($this->domains && !isset($this->domains[$name])) {
                continue;
            }

            if (isset($this->ignoredDomains[$name])) {
                continue;
            }

            foreach ($domain->all() as $message) {
                if ($current->has($message)) {
                    // FIXME: Compare what has changed

                    continue;
                }

                $newMessages[] = $message;
            }
        }

        $deletedMessages = array();
        foreach ($current->getDomains() as $name => $domain) {
            if ($this->domains && !isset($this->domains[$name])) {
                continue;
            }

            if (isset($this->ignoredDomains[$name])) {
                continue;
            }

            foreach ($domain->all() as $message) {
                if ($new->has($message)) {
                    continue;
                }

                $deletedMessages[] = $message;
            }
        }

        return new ChangeSet($newMessages, $deletedMessages);
    }
}
