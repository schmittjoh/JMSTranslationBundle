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

namespace JMS\TranslationBundle\Twig\Node;

class Transchoice extends \Twig_Node_Expression
{
    public function __construct(\Twig_Node_Expression_Array $arguments, $lineno)
    {
        parent::__construct(array('arguments' => $arguments), array(), $lineno);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->raw(
            sprintf(
                '$this->env->getExtension(\'%s\')->%s(',
                'JMS\TranslationBundle\Twig\TranslationExtension',
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
