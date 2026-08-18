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

namespace JMS\TranslationBundle\Tests\Twig;

use Twig\Node\Expression\Binary\EqualBinary;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\Ternary\ConditionalTernary;
use Twig\Node\Expression\TestExpression;
use Twig\Node\Node;
use Twig\Node\Nodes;
use Twig\TwigFilter;

class DefaultApplyingNodeVisitorTest extends BaseTwigTestCase
{
    public function testApply(): void
    {
        $this->assertEquals(
            $this->parse('apply_default_value_compiled.html.twig', true),
            $this->parse('apply_default_value.html.twig', true)
        );
    }

    /**
     * The visitor replaces the "desc" filter with a hand-built AST. Twig deprecated the node classes
     * and constructor signatures it used to rely on, so assert which node types are produced: a wrong
     * choice still compiles to the expected output (see testApply) while emitting deprecations.
     */
    public function testApplyBuildsAstWithNonDeprecatedNodes(): void
    {
        $conditions = $this->findConditionalTernaries($this->parseAst('apply_default_value.html.twig', true));

        // one for "|trans|desc(...)", one for "|trans({...})|desc(...)"
        self::assertCount(2, $conditions);

        // the replacements are stripped from the left-hand side of the comparison, which means
        // rebuilding the "trans" filter arguments: they must stay a node list Twig accepts
        $comparison = $this->unwrapTest($conditions[1]->getNode('test'));
        self::assertInstanceOf(EqualBinary::class, $comparison);
        self::assertInstanceOf(Nodes::class, $comparison->getNode('left')->getNode('arguments'));

        // the default value is wrapped in a "replace" filter, which since Twig 3.12 has to be
        // created from the environment's TwigFilter instance instead of from the filter name
        $replaceFilter = $this->unwrapEscape($conditions[1]->getNode('left'));
        self::assertInstanceOf(FilterExpression::class, $replaceFilter);
        self::assertSame('replace', $replaceFilter->getAttribute('name'));
        self::assertTrue(
            $replaceFilter->hasAttribute('twig_callable'),
            'The "replace" filter was not created from a TwigFilter instance.'
        );
        self::assertInstanceOf(TwigFilter::class, $replaceFilter->getAttribute('twig_callable'));
        self::assertInstanceOf(Nodes::class, $replaceFilter->getNode('arguments'));
    }

    /**
     * @return list<ConditionalTernary>
     */
    private function findConditionalTernaries(Node $node): array
    {
        $found = $node instanceof ConditionalTernary ? [$node] : [];

        foreach ($node as $child) {
            if ($child instanceof Node) {
                $found = array_merge($found, $this->findConditionalTernaries($child));
            }
        }

        return $found;
    }

    /**
     * Twig >= 3.21 wraps a ternary condition in a "true" test.
     */
    private function unwrapTest(Node $node): Node
    {
        return $node instanceof TestExpression ? $node->getNode('node') : $node;
    }

    /**
     * The output escaper wraps both ternary branches in an "escape" filter.
     */
    private function unwrapEscape(Node $node): Node
    {
        while (
            $node instanceof FilterExpression
            && 'escape' === $node->getAttribute('name')
        ) {
            $node = $node->getNode('node');
        }

        return $node;
    }
}
