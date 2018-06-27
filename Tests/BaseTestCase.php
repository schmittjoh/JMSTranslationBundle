<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 27/06/18
 * Time: 2:39 PM
 */

namespace JMS\TranslationBundle\Tests;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    public function createMock($class)
    {
        if (method_exists('PHPUnit\Framework\TestCase', 'createMock')) {
            return parent::createMock($class);
        }

        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();
    }
}
