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

use JMS\TranslationBundle\Translation\ConfigBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JMSTranslationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration($container), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('console.xml');

        $container->setParameter('jms_translation.source_language', $config['source_language']);
        $container->setParameter('jms_translation.locales', $config['locales']);

        foreach ($config['dumper'] as $option => $value) {
            $container->setParameter('jms_translation.dumper.' . $option, $value);
        }

        $requests = [];
        foreach ($config['configs'] as $name => $extractConfig) {
            $def = new Definition(ConfigBuilder::class);
            $def->addMethodCall('setTranslationsDir', [$extractConfig['output_dir']]);
            $def->addMethodCall('setScanDirs', [$extractConfig['dirs']]);

            if (isset($extractConfig['ignored_domains'])) {
                $ignored = [];
                foreach ($extractConfig['ignored_domains'] as $domain) {
                    $ignored[$domain] = true;
                }

                $def->addMethodCall('setIgnoredDomains', [$ignored]);
            }

            if (isset($extractConfig['domains'])) {
                $domains = [];
                foreach ($extractConfig['domains'] as $domain) {
                    $domains[$domain] = true;
                }

                $def->addMethodCall('setDomains', [$domains]);
            }

            if (isset($extractConfig['extractors'])) {
                $extractors = [];
                foreach ($extractConfig['extractors'] as $alias) {
                    $extractors[$alias] = true;
                }

                $def->addMethodCall('setEnabledExtractors', [$extractors]);
            }

            if (isset($extractConfig['excluded_dirs'])) {
                $def->addMethodCall('setExcludedDirs', [$extractConfig['excluded_dirs']]);
            }

            if (isset($extractConfig['excluded_names'])) {
                $def->addMethodCall('setExcludedNames', [$extractConfig['excluded_names']]);
            }

            if (isset($extractConfig['output_format'])) {
                $def->addMethodCall('setOutputFormat', [$extractConfig['output_format']]);
            }

            if (isset($extractConfig['default_output_format'])) {
                $def->addMethodCall('setDefaultOutputFormat', [$extractConfig['default_output_format']]);
            }

            if (isset($extractConfig['intl_icu'])) {
                $def->addMethodCall('setUseIcuMessageFormat', [$extractConfig['intl_icu']]);
            }

            if (isset($extractConfig['keep'])) {
                $def->addMethodCall('setKeepOldTranslations', [$extractConfig['keep']]);
            }

            if (isset($extractConfig['external_translations_dirs'])) {
                $def->addMethodCall('setLoadResources', [$extractConfig['external_translations_dirs']]);
            }

            $requests[$name] = $def;
        }

        $container
            ->getDefinition('jms_translation.config_factory')
            ->addArgument($requests);
    }
}
