<?php

declare(strict_types=1);

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
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MountDumpersPass());
    }

    /**
     * @test
     */
    public function ifCompilerPassCollectsServicesByArgumentTheseWillExist()
    {
        $collectingService = new Definition();
        $this->setDefinition('jms_translation.file_writer', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('jms_translation.dumper', ['format' => 'foo']);
        $this->setDefinition('service0', $collectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'jms_translation.file_writer',
            0,
            ['foo' => new Reference('service0')]
        );
    }
}
