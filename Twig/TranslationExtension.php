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

namespace JMS\TranslationBundle\Twig;

/**
 * Provides some extensions for specifying translation metadata.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TranslationExtension extends AbstractExtension
{
    private bool $debug;

    /**
     * @param bool $debug
     */
    public function __construct($translator, $debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * @return array
     */
    public function getNodeVisitors()
    {
        $visitors = [
            new NormalizingNodeVisitor(),
            new RemovingNodeVisitor(),
        ];

        if ($this->debug) {
            $visitors[] = new DefaultApplyingNodeVisitor();
        }

        return $visitors;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new TwigFilter('desc', [$this, 'desc']),
            new TwigFilter('meaning', [$this, 'meaning']),
        ];
    }

    /**
     * @param mixed $v
     *
     * @return mixed
     */
    public function desc($v)
    {
        return $v;
    }

    /**
     * @param mixed $v
     *
     * @return mixed
     */
    public function meaning($v)
    {
        return $v;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jms_translation';
    }
}
