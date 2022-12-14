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

namespace JMS\TranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Configuration implements ConfigurationInterface
{
    private $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $c = $this->container;

        $tb = new TreeBuilder('jms_translation');
        // Keep compatibility with symfony/config < 4.2
        if (!method_exists($tb, 'getRootNode')) {
            $rootNode = $tb->root('jms_translation');
        } else {
            $rootNode = $tb->getRootNode();
        }

        $rootNode
            ->fixXmlConfig('config')
            ->children()
                ->arrayNode('locales')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('dumper')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('add_date')->defaultTrue()->end()
                        ->booleanNode('add_references')->defaultTrue()->end()
                    ->end()
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
                                        ->always(static function ($v) use ($c) {
                                            $v = str_replace(DIRECTORY_SEPARATOR, '/', $v);

                                            if ('@' === $v[0]) {
                                                if (false === $pos = strpos($v, '/')) {
                                                    $bundleName = substr($v, 1);
                                                } else {
                                                    $bundleName = substr($v, 1, $pos - 1);
                                                }

                                                $bundles = $c->getParameter('kernel.bundles');
                                                if (!isset($bundles[$bundleName])) {
                                                    throw new \Exception(sprintf('The bundle "%s" does not exist. Available bundles: %s', $bundleName, implode(', ', array_keys($bundles))));
                                                }

                                                $ref = new \ReflectionClass($bundles[$bundleName]);
                                                $v = false === $pos ? dirname($ref->getFileName()) : dirname($ref->getFileName()) . substr($v, $pos);
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
                            ->scalarNode('intl_icu')->defaultValue(false)->end()
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
            ->end();

        return $tb;
    }
}
