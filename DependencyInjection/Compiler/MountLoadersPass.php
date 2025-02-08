<?php

declare(strict_types=1);

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
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MountLoadersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('jms_translation.loader_manager')) {
            return;
        }

        $loaders = [];
        $i = 0;

        foreach ($container->findTaggedServiceIds('jms_translation.loader') as $id => $attrs) {
            foreach ($attrs as $attr) {
                if (!isset($attr['format'])) {
                    throw new RuntimeException(sprintf('The attribute "format" must be defined for tag "jms_translation.loader" for service "%s".', $id));
                }

                $loaders[$attr['format']] = new Reference($id);
            }
        }

        foreach ($container->findTaggedServiceIds('translation.loader') as $id => $attrs) {
            foreach ($attrs as $attr) {
                if (!isset($attr['alias'])) {
                    throw new RuntimeException(sprintf('The attribute "alias" must be defined for tag "translation.loader" for service "%s".', $id));
                }
                if (isset($loaders[$attr['alias']])) {
                    continue;
                }

                $def = new ChildDefinition('jms_translation.loader.symfony_adapter');
                $def->addArgument(new Reference($id));
                $container->setDefinition($id = 'jms_translation.loader.wrapped_symfony_loader.' . ($i++), $def);

                $loaders[$attr['alias']] = new Reference($id);
                if (isset($attr['legacy_alias']) && !isset($loaders[$attr['legacy_alias']])) {
                    $loaders[$attr['legacy_alias']] = new Reference($id);
                }
            }
        }

        $container
            ->getDefinition('jms_translation.loader_manager')
            ->addArgument($loaders);
    }
}
