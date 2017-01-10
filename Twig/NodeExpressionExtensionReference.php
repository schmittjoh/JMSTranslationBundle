<?php

/*
 * Copied from twigphp/twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JMS\TranslationBundle\Twig;

/**
 * Represents an extension call node.
 * Deprecated since twig 1.23 and removed in 2.0.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 */
class NodeExpressionExtensionReference extends \Twig_Node_Expression
{
    public function __construct($name, $lineno, $tag = null)
    {
        parent::__construct(array(), array('name' => $name), $lineno, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->raw(sprintf("\$this->env->getExtension('%s')", $this->getAttribute('name')));
    }
}
