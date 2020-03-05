<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Functional;

use JMS\TranslationBundle\Translation\ConfigFactory;
use JMS\TranslationBundle\Translation\Updater;
use JMS\TranslationBundle\Twig\TranslationExtension;

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
        return [
            ['jms_translation.updater', Updater::class],
            ['jms_translation.config_factory', ConfigFactory::class],
            ['jms_translation.twig_extension', TranslationExtension::class],
        ];
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
