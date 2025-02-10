<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Functional;

use JMS\TranslationBundle\Translation\ConfigFactory;
use JMS\TranslationBundle\Twig\TranslationExtension;
use PHPUnit\Framework\Attributes\DataProvider;

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

    public static function provider(): array
    {
        return [
            ['jms_translation.config_factory', ConfigFactory::class],
            ['jms_translation.twig_extension', TranslationExtension::class],
        ];
    }

    #[DataProvider('provider')]
    public function testServiceExists(string $serviceId, string $class): void
    {
        $container = static::$kernel->getContainer();
        $this->assertTrue($container->has($serviceId));
        $service = $container->get($serviceId);
        $this->assertInstanceOf($class, $service);
    }
}
