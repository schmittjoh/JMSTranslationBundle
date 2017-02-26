<?php

namespace JMS\TranslationBundle\Tests\DependencyInjection\Compiler;

use JMS\TranslationBundle\DependencyInjection\Compiler\MountLoadersPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MountLoadersPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MountLoadersPass());
    }

    /**
     * @test
     */
    public function if_compiler_pass_collects_services_by_argument_these_will_exist()
    {
        $collectingService = new Definition();
        $this->setDefinition('jms_translation.loader_manager', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('jms_translation.loader', array('format' => 'foo'));
        $this->setDefinition('service0', $collectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'jms_translation.loader_manager',
            0,
            array('foo' => new Reference('service0'))
        );
    }
}
