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
                        'formatter' => 'dropdown',
                        'links' => array(
                            /** @Desc("This is a drop-down link name") */
                            'dummy.linka' => array(
                                'link' => function (TableRowInterface $row)
                                {
                                    return $row->getField('dummyextlink');
                                }
                            ),
                            'dummy.linkb' => array(
                                'component' => 'dummy',
                                'page' => 'delete',
                                'route_params' => array(
                                    'dummy' => '%id%',
                                )
                            ),
                        )
                    ),
                    'sortable' => true,
                )
            )
            ->addColumn(
                '', 'def', array(
                    'formatter' => array(
                        'formatter' => 'icon',
                        'icons' => function ($value, TableRowInterface $row)
                        {
                            $icons = array();

                            switch ($row->getField('type'))
                            {
                                case 'a':
                                    /** @Desc("Dummy icon desc") */
                                    $icons[] = array(
                                        'icon' => 'a',
                                        'title' => 'dummy.icon.a'
                                    );
                                    break;
                                case 'bcd':
                                    $icons[] = array(
                                        'icon' => 'bcd',
                                        'title' => 'dummy.icon.bcd'
                                    );
                                    break;
                            }

                            if ($row->getField('archived'))
                            {
                                $icons[] = 'archive';
                            }

                            return $icons;
                        }
                    ),
                )
            )
            ->addColumn(
                /** @Desc("Ghi") */
                'dummy.ghi', 'ghi'
            );
    }
}
