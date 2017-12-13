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

use JMS\TranslationBundle\Exception\InvalidArgumentException;

class ConfigFactory
{
    /**
     * @var array
     */
    private $builders;

    /**
     * ConfigFactory constructor.
     * @param array $builders ConfigBuilder
     */
    public function __construct(array $builders = array())
    {
        $this->builders = $builders;
    }

    /**
     * @return array of strings
     */
    public function getNames()
    {
        return array_keys($this->builders);
    }

    /**
     * @param $name
     * @return ConfigBuilder
     * @throws InvalidArgumentException
     */
    public function getBuilder($name)
    {
        if (!isset($this->builders[$name])) {
            throw new InvalidArgumentException(sprintf('There has no extraction config with name "%s" been configured. Available configs: %s', $name, implode(', ', array_keys($this->builders))));
        }

        return $this->builders[$name];
    }

    /**
     * @param $name
     * @param $locale
     * @return Config
     */
    public function getConfig($name, $locale)
    {
        return $this->getBuilder($name)->setLocale($locale)->getConfig();
    }

    /**
     * @param string $name
     * @param ConfigBuilder $builder
     */
    public function addBuilder($name, $builder)
    {
        $this->builders[$name] = $builder;
    }
}
