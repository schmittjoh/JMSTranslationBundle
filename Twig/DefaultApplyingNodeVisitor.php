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

use JMS\TranslationBundle\Exception\RuntimeException;
use Twig\Environment;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\Binary\EqualBinary;
use Twig\Node\Expression\ConditionalExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * Applies the value of the "desc" filter if the "trans" filter has no
 * translations.
 *
 * This is only active in your development environment.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class DefaultApplyingNodeVisitor implements NodeVisitorInterface
{
    private bool $enabled = true;

    public function setEnabled($bool)
    {
        $this->enabled = (bool) $bool;
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        if (!$this->enabled) {
            return $node;
        }

        if (
            $node instanceof FilterExpression
                && 'desc' === ($node->hasAttribute('name') ? $node->getAttribute('name') : $node->getNode('filter')->getAttribute('value'))
        ) {
            $transNode = $node->getNode('node');
            while (
                $transNode instanceof FilterExpression
                    && !in_array($transNode->hasAttribute('name') ? $transNode->getAttribute('name') : $transNode->getNode('filter')->getAttribute('value'), ['trans'], true)
            ) {
                $transNode = $transNode->getNode('node');
            }

            if (!$transNode instanceof FilterExpression) {
                throw new RuntimeException(sprintf('The "desc" filter in "%s" line %d must be applied after a "trans" filter.', $node->getTemplateName(), $node->getTemplateLine()));
            }

            $wrappingNode = $node->getNode('node');

            $testNode     = clone $wrappingNode;
            $arguments    = iterator_to_array($node->getNode('arguments'));
            $defaultNode  = $arguments[0];

            $wrappingNodeArguments = iterator_to_array($wrappingNode->getNode('arguments'));

            // if the |trans filter has replacements parameters
            // (e.g. |trans({'%foo%': 'bar'}))
            if (isset($wrappingNodeArguments[0])) {
                $lineno =  $wrappingNode->getTemplateLine();

                // remove the replacements from the test node
                $testNodeArguments    = iterator_to_array($testNode->getNode('arguments'));
                $testNodeArguments[0] = new ArrayExpression([], $lineno);
                $testNode->setNode('arguments', new Node($testNodeArguments));

                // wrap the default node in a |replace filter
                $defaultNode = new FilterExpression(
                    $arguments[0],
                    new ConstantExpression('replace', $lineno),
                    new Node([$wrappingNodeArguments[0]]),
                    $lineno
                );
            }

            $condition = new ConditionalExpression(
                new EqualBinary($testNode, $transNode->getNode('node'), $wrappingNode->getTemplateLine()),
                $defaultNode,
                clone $wrappingNode,
                $wrappingNode->getTemplateLine()
            );
            $node->setNode('node', $condition);
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): Node
    {
        return $node;
    }

    public function getPriority(): int
    {
        return -2;
    }
}
