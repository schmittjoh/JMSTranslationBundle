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

namespace JMS\TranslationBundle\Annotation;

use JMS\TranslationBundle\Exception\RuntimeException;

/**
 * @Annotation
 *
 * @author Pol-Valentin Cami <polvalentin@gmail.com>
 */
final class Domains
{
    /** @var array @Required */
    public $texts;

    public function __construct($values)
    {
        if (0 === func_num_args()) {
            return;
        }
        $values = func_get_arg(0);

        foreach ($values['value'] as $var) {
            if( ! is_scalar($var)) {
                throw new \InvalidArgumentException(sprintf(
                    '@Domains supports only scalar values "%s" given.',
                    is_object($var) ? get_class($var) : gettype($var)
                ));
            }
        }
        if (isset($values['value'])) {
            $values['text'] = $values['value'];
        }

        if (!isset($values['text'])) {
            throw new RuntimeException(sprintf('The "text" attribute for annotation "@Domain" must be set.'));
        }

        $this->texts = $values['text'];
    }
}