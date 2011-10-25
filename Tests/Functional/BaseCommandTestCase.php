<?php

namespace JMS\TranslationBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;

abstract class BaseCommandTestCase extends BaseTestCase
{
    protected function getApp(array $options = array())
    {
        $kernel = $this->createKernel($options);
        $kernel->boot();

        $app = new Application($kernel);
        $app->setAutoExit(false);

        return $app;
    }
}