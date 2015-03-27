<?php

namespace StreamOne\DummyBundle\Table;

/**
 * Table definition for the dummy list table
 */
class DummyTableDefinition implements TableDefinitionInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilder $builder)
    {
        $builder
            ->setTableId('dummy')
            ->setDefaultSortColumn('title')
            ->setPaginationEnabled()
            ->addColumn('', 'xyz', array())
            ->addColumn('dummy.xyz', 'xyz', array())
            ->addColumn(
                /** @Desc("This is abc") */
                'dummy.abc', 'abc', array(
                    'formatter' => array(
                        'formatter' => 'link',
                        'component' => 'dummy',
                        'page' => 'detail',
                        'route_params' => array(
                            'dummy' => '%id%',
                        ),
                        'name_formatter' => array(
                            'formatter' => 'date',
                            'date_format' => \IntlDateFormatter::SHORT,
                            'time_format' => \IntlDateFormatter::SHORT,
                        ),
                    ),
                    'sortable' => true,
                    'sort_field' => 'created',
                )
            )
            ->addColumn(
                /** @Desc("This is a test") */
                'dummy.def', 'def', array(
                    'formatter' => array(
                        'formatter' => 'link',
                        'component' => 'dummy',
                        'page' => 'detail',
                        'route_params' => array(
                            'dummy' => '%id%',
                        )
                    ),
                    'sortable' => true,
                )
            )
            ->addColumn(
                /** @Desc("Ghi") */
                'dummy.ghi', 'ghi'
            );
    }
}
