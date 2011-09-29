<?php

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
    public function enterNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        return $node;
    }

    public function leaveNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Expression_Binary_Concat
            && ($left = $node->getNode('left')) instanceof \Twig_Node_Expression_Constant
            && ($right = $node->getNode('right')) instanceof \Twig_Node_Expression_Constant) {
            return new \Twig_Node_Expression_Constant($left->getAttribute('value').$right->getAttribute('value'), $left->getLine());
        }

        return $node;
    }

    public function getPriority()
    {
        return -1;
    }
}