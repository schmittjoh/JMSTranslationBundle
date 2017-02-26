<?php

namespace JMS\TranslationBundle\Tests\DependencyInjection\Compiler;

use JMS\TranslationBundle\DependencyInjection\Compiler\MountFileVisitorsPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MountFileVisitorsPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MountFileVisitorsPass());
    }

    /**
     * @test
     */
    public function if_compiler_pass_collects_services_by_argument_these_will_exist()
    {
        $collectingService = new Definition();
        $this->setDefinition('jms_translation.extractor.file_extractor', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('jms_translation.file_visitor');
        $this->setDefinition('service0', $collectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'jms_translation.extractor.file_extractor',
            0,
            array(new Reference('service0'))
        );
    }
}
