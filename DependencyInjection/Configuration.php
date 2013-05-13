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

namespace JMS\TranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function getConfigTreeBuilder()
    {
        $c = $this->container;

        $tb = new TreeBuilder();
        $tb
            ->root('jms_translation')
                ->fixXmlConfig('config')
                ->children()
                    ->arrayNode('locales')
                        ->prototype('scalar')->end()
                    ->end()
                    ->scalarNode('source_language')->defaultValue('en')->end()
                    ->arrayNode('configs')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->fixXmlConfig('dir', 'dirs')
                            ->fixXmlConfig('excluded_dir')
                            ->fixXmlConfig('excluded_name')
                            ->fixXmlConfig('ignore_domain')
                            ->fixXmlConfig('external_translations_dir')
                            ->fixXmlConfig('domain')
                            ->fixXmlConfig('extractor')
                            ->children()
                                ->arrayNode('extractors')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('dirs')
                                    ->requiresAtLeastOneElement()
                                    ->prototype('scalar')
                                        ->validate()
                                            ->always(function($v) use ($c) {
                                                $v = str_replace(DIRECTORY_SEPARATOR, '/', $v);

                                                if ('@' === $v[0]) {
                                                    if (false === $pos = strpos($v, '/')) {
                                                        $bundleName = substr($v, 1);
                                                    } else {
                                                        $bundleName = substr($v, 1, $pos - 2);
                                                    }

                                                    $bundles = $c->getParameter('kernel.bundles');
                                                    if (!isset($bundles[$bundleName])) {
                                                        throw new \Exception(sprintf('The bundle "%s" does not exist. Available bundles: %s', $bundleName, array_keys($bundles)));
                                                    }

                                                    $ref = new \ReflectionClass($bundles[$bundleName]);
                                                    $v = false === $pos ? dirname($ref->getFileName()) : dirname($ref->getFileName()).substr($v, $pos);
                                                }

                                                if (!is_dir($v)) {
                                                    throw new \Exception(sprintf('The directory "%s" does not exist.', $v));
                                                }

                                                return $v;
                                            })
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('excluded_dirs')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('excluded_names')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('external_translations_dirs')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->scalarNode('output_format')->end()
                                ->scalarNode('default_output_format')->end()
                                ->arrayNode('ignored_domains')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('domains')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->scalarNode('output_dir')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('keep')->defaultValue(false)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $tb;
    }
}