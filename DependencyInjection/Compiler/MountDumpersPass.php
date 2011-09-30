<?php

namespace JMS\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MountDumpersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jms_translation.file_writer')) {
            return;
        }

        $dumpers = array();
        $i = 0;
        foreach ($container->findTaggedServiceIds('translation.dumper') as $id => $attr) {
            if (!isset($attr[0]['alias'])) {
                throw new \RuntimeException(sprintf('The "alias" attribute must be set for tag "translation.dumper" for service "%s".', $id));
            }

            $def = new DefinitionDecorator('jms_translation.dumper.symfony_adapter');
            $def->addArgument(new Reference($id))->addArgument($attr[0]['alias']);
            $container->setDefinition($id = 'jms_translation.dumper.wrapped_symfony_dumper.'.($i++), $def);

            $dumpers[$attr[0]['alias']] = new Reference($id);
        }

        foreach ($container->findTaggedServiceIds('jms_translation.dumper') as $id => $attr) {
            if (!isset($attr[0]['format'])) {
                throw new \RuntimeException(sprintf('The "format" attribute must be set for tag "jms_translation.dumper" for service "%s".', $id));
            }

            $dumpers[$attr[0]['format']] = new Reference($id);
        }

        $container
            ->getDefinition('jms_translation.file_writer')
            ->addArgument($dumpers)
        ;
    }
}