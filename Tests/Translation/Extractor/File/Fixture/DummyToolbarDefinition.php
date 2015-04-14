<?php

namespace StreamOne\DummyBundle\Table;

/**
 * Toolbar definition for the dummy list table
 */
class DummyToolbarDefinition implements TableDefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildToolbar(toolbarBuilder $builder)
    {
        $builder
            ->addButton('dummy.xyz', 'xyz', 'component', 'page', array())
            ->addButton(
                /** @Desc("This is abc") */
                'dummy.abc', 'abc', 'abc', 'def', array(
                    'dummy' => '%id%',
                )
            )
            ->addButton(
                /** @Desc("This is a test") */
                'dummy.def', 'def', 'ghi', 'jkl', array(
                    'dummy' => '%id%',
                )
            )
            ->addButton(
                /** @Desc("Ghi") */
                'dummy.ghi', 'ghi', 'ghi', 'ghi'
            );
    }
}
