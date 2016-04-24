<?php

namespace JMS\TranslationBundle\Tests\Functional;

/**
 * Make sure we instantiate services.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ServiceInstantiationTest extends BaseTestCase
{
    public function setUp()
    {
        static::createClient();
    }

    public function provider()
    {
        return array(
            array('jms_translation.updater', 'JMS\TranslationBundle\Translation\Updater'),
            array('jms_translation.config_factory', 'JMS\TranslationBundle\Translation\ConfigFactory'),
            array('jms_translation.twig_extension', 'JMS\TranslationBundle\Twig\TranslationExtension'),
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
