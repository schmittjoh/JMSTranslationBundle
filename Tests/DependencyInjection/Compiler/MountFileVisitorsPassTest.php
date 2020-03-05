<?php

declare(strict_types=1);

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
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MountFileVisitorsPass());
    }

    /**
     * @test
     */
    public function ifCompilerPassCollectsServicesByArgumentTheseWillExist()
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
            [new Reference('service0')]
        );
    }
}
