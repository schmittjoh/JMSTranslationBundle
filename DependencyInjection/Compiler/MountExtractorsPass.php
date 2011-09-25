<?php

namespace JMS\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MountExtractorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jms_translation.extractor_manager')) {
            return;
        }

        $def = $container->getDefinition('jms_translation.extractor_manager');
        $extractors = array();
        foreach ($container->findTaggedServiceIds('jms_translation.extractor') as $id => $attr) {
            if (!isset($attr[0]['alias'])) {
                throw new \RuntimeException(sprintf('The "alias" attribute must be set for tag "jms_translation.extractor" of service "%s".', $id));
            }

            $extractors[$attr[0]['alias']] = new Reference($id);
        }

        $def->addArgument($extractors);
    }
}