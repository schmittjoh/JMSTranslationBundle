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

class MountDumpersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('jms_translation.file_writer')) {
            return;
        }

        $dumpers = [];
        $i = 0;
        foreach ($container->findTaggedServiceIds('translation.dumper') as $id => $attr) {
            if (!isset($attr[0]['alias'])) {
                throw new RuntimeException(sprintf('The "alias" attribute must be set for tag "translation.dumper" for service "%s".', $id));
            }

            $def = new ChildDefinition('jms_translation.dumper.symfony_adapter');
            $def->addArgument(new Reference($id))->addArgument($attr[0]['alias']);
            $container->setDefinition($id = 'jms_translation.dumper.wrapped_symfony_dumper.' . ($i++), $def);

            $dumpers[$attr[0]['alias']] = new Reference($id);
        }

        foreach ($container->findTaggedServiceIds('jms_translation.dumper') as $id => $attr) {
            if (!isset($attr[0]['format'])) {
                throw new RuntimeException(sprintf('The "format" attribute must be set for tag "jms_translation.dumper" for service "%s".', $id));
            }

            $dumpers[$attr[0]['format']] = new Reference($id);
        }

        $container
            ->getDefinition('jms_translation.file_writer')
            ->addArgument($dumpers);
    }
}
