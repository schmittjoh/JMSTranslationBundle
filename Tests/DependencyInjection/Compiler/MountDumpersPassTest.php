<?php

namespace JMS\TranslationBundle\Tests\DependencyInjection\Compiler;

use JMS\TranslationBundle\DependencyInjection\Compiler\MountDumpersPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MountDumpersPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MountDumpersPass());
    }

    /**
     * @test
     */
    public function if_compiler_pass_collects_services_by_argument_these_will_exist()
    {
        $collectingService = new Definition();
        $this->setDefinition('jms_translation.file_writer', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('jms_translation.dumper', array('format' => 'foo'));
        $this->setDefinition('service0', $collectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'jms_translation.file_writer',
            0,
            array('foo' => new Reference('service0'))
        );
    }
}
