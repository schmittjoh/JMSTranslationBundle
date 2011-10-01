<?php

namespace JMS\TranslationBundle\Twig;

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
    public function enterNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Expression_Filter
                && 'desc' === $node->getNode('filter')->getAttribute('value')) {

            $transNode = $node->getNode('node');
            while ($transNode instanceof \Twig_Node_Expression_Filter
                       && 'trans' !== $transNode->getNode('filter')->getAttribute('value')) {
                $transNode = $transNode->getNode('node');
            }

            if (!$transNode instanceof \Twig_Node_Expression_Filter) {
                throw new \RuntimeException(sprintf('The "desc" filter must be applied after a "trans" filter.'));
            }

            $wrappingNode = $node->getNode('node');
            $default = $transNode->getNode('node');

            $condition = new \Twig_Node_Expression_Conditional(
                new \Twig_Node_Expression_Binary_Equal($wrappingNode, $transNode->getNode('node'), $wrappingNode->getLine()),
                clone $node->getNode('arguments')->getNode(0),
                clone $wrappingNode,
                $wrappingNode->getLine()
            );
            $node->setNode('node', $condition);
        }

        return $node;
    }

    public function leaveNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        return $node;
    }

    public function getPriority()
    {
        return -2;
    }
}