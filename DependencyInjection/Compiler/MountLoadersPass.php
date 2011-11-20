<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\TranslationBundle\DependencyInjection\Compiler;

use JMS\TranslationBundle\Exception\RuntimeException;
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
        $i = 0;
        foreach ($container->findTaggedServiceIds('translation.loader') as $id => $attr) {
            if (!isset($attr[0]['alias'])) {
                throw new RuntimeException(sprintf('The attribute "alias" must be defined for tag "translation.loader" for service "%s".', $id));
            }

            $def = new DefinitionDecorator('jms_translation.loader.symfony_adapter');
            $def->addArgument(new Reference($id));
            $container->setDefinition($id = 'jms_translation.loader.wrapped_symfony_loader.'.($i++), $def);

            $loaders[$attr[0]['alias']] = new Reference($id);
        }

        foreach ($container->findTaggedServiceIds('jms_translation.loader') as $id => $attr) {
            if (!isset($attr[0]['format'])) {
                throw new RuntimeException(sprintf('The attribute "format" must be defined for tag "jms_translation.loader" for service "%s".', $id));
            }

            $loaders[$attr[0]['format']] = new Reference($id);
        }

        $container
            ->getDefinition('jms_translation.loader_manager')
            ->addArgument($loaders)
        ;
    }
}