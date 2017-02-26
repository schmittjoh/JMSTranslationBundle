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

use JMS\TranslationBundle\Exception\RuntimeException;

/**
 * Applies the value of the "desc" filter if the "trans" filter has no
 * translations.
 *
 * This is only active in your development environment.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class DefaultApplyingNodeVisitor implements \Twig_NodeVisitorInterface
{
    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @param $bool
     */
    public function setEnabled($bool)
    {
        $this->enabled = (bool) $bool;
    }

    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return \Twig_Node
     */
    public function enterNode(\Twig_Node $node, \Twig_Environment $env)
    {
        if (!$this->enabled) {
            return $node;
        }

        if ($node instanceof \Twig_Node_Expression_Filter
                && 'desc' === $node->getNode('filter')->getAttribute('value')) {
            $transNode = $node->getNode('node');
            while ($transNode instanceof \Twig_Node_Expression_Filter
                       && 'trans' !== $transNode->getNode('filter')->getAttribute('value')
                       && 'transchoice' !== $transNode->getNode('filter')->getAttribute('value')) {
                $transNode = $transNode->getNode('node');
            }

            if (!$transNode instanceof \Twig_Node_Expression_Filter) {
                throw new RuntimeException(sprintf('The "desc" filter must be applied after a "trans", or "transchoice" filter.'));
            }

            $wrappingNode = $node->getNode('node');
            $testNode = clone $wrappingNode;
            $defaultNode = $node->getNode('arguments')->getNode(0);

            // if the |transchoice filter is used, delegate the call to the TranslationExtension
            // so that we can catch a possible exception when the default translation has not yet
            // been extracted
            if ('transchoice' === $transNode->getNode('filter')->getAttribute('value')) {
                $transchoiceArguments = new \Twig_Node_Expression_Array(array(), $transNode->getLine());
                $transchoiceArguments->addElement($wrappingNode->getNode('node'));
                $transchoiceArguments->addElement($defaultNode);
                foreach ($wrappingNode->getNode('arguments') as $arg) {
                    $transchoiceArguments->addElement($arg);
                }

                $transchoiceNode = new \Twig_Node_Expression_MethodCall(
                    new \Twig_Node_Expression_ExtensionReference('jms_translation', $transNode->getLine()),
                    'transchoiceWithDefault', $transchoiceArguments, $transNode->getLine());
                $node->setNode('node', $transchoiceNode);

                return $node;
            }

            // if the |trans filter has replacements parameters
            // (e.g. |trans({'%foo%': 'bar'}))
            if ($wrappingNode->getNode('arguments')->hasNode(0)) {
                $lineno =  $wrappingNode->getLine();

                // remove the replacements from the test node
                $testNode->setNode('arguments', clone $testNode->getNode('arguments'));
                $testNode->getNode('arguments')->setNode(0, new \Twig_Node_Expression_Array(array(), $lineno));

                // wrap the default node in a |replace filter
                $defaultNode = new \Twig_Node_Expression_Filter(
                    clone $node->getNode('arguments')->getNode(0),
                    new \Twig_Node_Expression_Constant('replace', $lineno),
                    new \Twig_Node(array(
                        clone $wrappingNode->getNode('arguments')->getNode(0)
                    )),
                    $lineno
                );
            }

            $condition = new \Twig_Node_Expression_Conditional(
                new \Twig_Node_Expression_Binary_Equal($testNode, $transNode->getNode('node'), $wrappingNode->getLine()),
                $defaultNode,
                clone $wrappingNode,
                $wrappingNode->getLine()
            );
            $node->setNode('node', $condition);
        }

        return $node;
    }

    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return \Twig_Node
     */
    public function leaveNode(\Twig_Node $node, \Twig_Environment $env)
    {
        return $node;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return -2;
    }
}
