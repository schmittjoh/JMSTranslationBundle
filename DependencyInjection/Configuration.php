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
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class Configuration implements ConfigurationInterface
{
    public function __construct(
        /** @var array<string, class-string<BundleInterface>> */
        private array $bundles,
    ) {
    }

    #[\Override()]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jms_translation');
        $rootNode = $treeBuilder->getRootNode();

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
                                        ->always(function ($v): string {
                                            $v = str_replace(DIRECTORY_SEPARATOR, '/', $v);

                                            if ('@' === $v[0]) {
                                                if (false === $pos = strpos($v, '/')) {
                                                    $bundleName = substr($v, 1);
                                                } else {
                                                    $bundleName = substr($v, 1, $pos - 1);
                                                }

                                                if (null === $bundleClass = ($this->bundles[$bundleName] ?? null)) {
                                                    throw new \Exception(sprintf('The bundle "%s" does not exist. Available bundles: %s', $bundleName, implode(', ', array_keys($this->bundles))));
                                                }

                                                $ref = new \ReflectionClass($bundleClass);
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

        return $treeBuilder;
    }
}
