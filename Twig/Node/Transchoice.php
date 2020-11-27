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

namespace JMS\TranslationBundle\Twig\Node;

use JMS\TranslationBundle\Twig\TranslationExtension;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ArrayExpression;

class Transchoice extends AbstractExpression
{
    public function __construct(ArrayExpression $arguments, $lineno)
    {
        parent::__construct(['arguments' => $arguments], [], $lineno);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->raw(
            sprintf(
                '$this->env->getExtension(\'%s\')->%s(',
                TranslationExtension::class,
                'transchoiceWithDefault'
            )
        );

        $first = true;
        foreach ($this->getNode('arguments')->getKeyValuePairs() as $pair) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $first = false;

            $compiler->subcompile($pair['value']);
        }

        $compiler->raw(')');
    }
}
