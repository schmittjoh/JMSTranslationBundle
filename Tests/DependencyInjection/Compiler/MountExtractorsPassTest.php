<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\DependencyInjection\Compiler;

use JMS\TranslationBundle\DependencyInjection\Compiler\MountExtractorsPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class MountExtractorsPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MountExtractorsPass());
    }

    /**
     * @test
     */
    public function ifCompilerPassCollectsServicesByArgumentTheseWillExist()
    {
        $collectingService = new Definition();
        $this->setDefinition('jms_translation.extractor_manager', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('jms_translation.extractor', ['alias' => 'foo']);
        $this->setDefinition('service0', $collectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'jms_translation.extractor_manager',
            0,
            ['foo' => new Reference('service0')]
        );
    }
}
