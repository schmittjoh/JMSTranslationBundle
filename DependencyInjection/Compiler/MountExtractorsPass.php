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
                throw new RuntimeException(sprintf('The "alias" attribute must be set for tag "jms_translation.extractor" of service "%s".', $id));
            }

            $extractors[$attr[0]['alias']] = new Reference($id);
        }

        $def->addArgument($extractors);
    }
}
