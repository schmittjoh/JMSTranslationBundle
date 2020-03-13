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

use Twig\Environment;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\AbstractNodeVisitor;

/**
 * Removes translation metadata filters from the AST.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class RemovingNodeVisitor extends AbstractNodeVisitor
{
    /**
     * @var bool
     */
    private $enabled = true;

    public function setEnabled($bool)
    {
        $this->enabled = (bool) $bool;
    }

    /**
     * @return Node
     */
    protected function doEnterNode(Node $node, Environment $env)
    {
        if ($this->enabled && $node instanceof FilterExpression) {
            $name = $node->getNode('filter')->getAttribute('value');

            if ('desc' === $name || 'meaning' === $name) {
                return $this->enterNode($node->getNode('node'), $env);
            }
        }

        return $node;
    }

    /**
     * @return Node
     */
    protected function doLeaveNode(Node $node, Environment $env)
    {
        return $node;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return -1;
    }
}
