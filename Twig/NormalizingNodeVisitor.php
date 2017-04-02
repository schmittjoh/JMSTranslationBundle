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

namespace JMS\TranslationBundle\Twig;

/**
 * Performs equivalence transformations on the AST to ensure that
 * subsequent visitors do not need to be aware of different syntaxes.
 *
 * E.g. "foo" ~ "bar" ~ "baz" would become "foobarbaz"
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class NormalizingNodeVisitor implements \Twig_NodeVisitorInterface
{
    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return \Twig_Node
     */
    public function enterNode(\Twig_Node $node, \Twig_Environment $env)
    {
        return $node;
    }

    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return \Twig_Node_Expression_Constant|\Twig_Node
     */
    public function leaveNode(\Twig_Node $node, \Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Expression_Binary_Concat
            && ($left = $node->getNode('left')) instanceof \Twig_Node_Expression_Constant
            && ($right = $node->getNode('right')) instanceof \Twig_Node_Expression_Constant) {
            return new \Twig_Node_Expression_Constant($left->getAttribute('value').$right->getAttribute('value'), $left->getLine());
        }

        return $node;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return -3;
    }
}
