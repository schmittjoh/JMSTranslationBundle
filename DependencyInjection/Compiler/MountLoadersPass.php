<?php

namespace JMS\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MountLoadersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(('jms_translation.loader_manager'))) {
            return;
        }

        $loaders = array();
        foreach ($container->findTaggedServiceIds('translation.loader') as $id => $attr) {
            if (!isset($attr[0]['alias'])) {
                throw new \RuntimeException(sprintf('The attribute "alias" must be defined for tag "translation.loader" for service "%s".', $id));
            }

            $def = new DefinitionDecorator('jms_translation.loader.symfony_adapter');
            $def->addArgument(new Reference($id));
            $loaders[$attr[0]['alias']] = $def;
        }

        foreach ($container->findTaggedServiceIds('jms_translation.loader') as $id => $attr) {
            if (!isset($attr[0]['format'])) {
                throw new \RuntimeException(sprintf('The attribute "format" must be defined for tag "jms_translation.loader" for service "%s".', $id));
            }

            $loaders[$attr[0]['format']] = new Reference($id);
        }

        $container
            ->getDefinition('jms_translation.loader_manager')
            ->addArgument($loaders)
        ;
    }
}