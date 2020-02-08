<?php

namespace JMS\TranslationBundle\Tests\Functional;

use JMS\TranslationBundle\Twig\TranslationExtension;
use JMS\TranslationBundle\Translation\ConfigFactory;
use JMS\TranslationBundle\Translation\Updater;

/**
 * Make sure we instantiate services.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ServiceInstantiationTest extends BaseTestCase
{
    protected function setUp(): void
    {
        static::createClient();
    }

    public function provider()
    {
        return array(
            array('jms_translation.updater', Updater::class),
            array('jms_translation.config_factory', ConfigFactory::class),
            array('jms_translation.twig_extension', TranslationExtension::class),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testServiceExists($serviceId, $class)
    {
        $container = static::$kernel->getContainer();
        $this->assertTrue($container->has($serviceId));
        $service = $container->get($serviceId);
        $this->assertInstanceOf($class, $service);
    }
}
