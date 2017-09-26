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

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JMSTranslationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration($container), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (!class_exists('Symfony\Component\ClassLoader\ClassLoader')) {
            $loader->load('console.xml');
        }

        $container->setParameter('jms_translation.source_language', $config['source_language']);
        $container->setParameter('jms_translation.locales', $config['locales']);

        foreach ($config['dumper'] as $option => $value) {
            $container->setParameter("jms_translation.dumper.{$option}", $value);
        }

        $requests = array();
        foreach ($config['configs'] as $name => $extractConfig) {
            $def = new Definition('JMS\TranslationBundle\Translation\ConfigBuilder');
            $def->addMethodCall('setTranslationsDir', array($extractConfig['output_dir']));
            $def->addMethodCall('setScanDirs', array($extractConfig['dirs']));

            if (isset($extractConfig['ignored_domains'])) {
                $ignored = array();
                foreach ($extractConfig['ignored_domains'] as $domain) {
                    $ignored[$domain] = true;
                }

                $def->addMethodCall('setIgnoredDomains', array($ignored));
            }

            if (isset($extractConfig['domains'])) {
                $domains = array();
                foreach ($extractConfig['domains'] as $domain) {
                    $domains[$domain] = true;
                }

                $def->addMethodCall('setDomains', array($domains));
            }

            if (isset($extractConfig['extractors'])) {
                $extractors = array();
                foreach ($extractConfig['extractors'] as $alias) {
                    $extractors[$alias] = true;
                }

                $def->addMethodCall('setEnabledExtractors', array($extractors));
            }

            if (isset($extractConfig['excluded_dirs'])) {
                $def->addMethodCall('setExcludedDirs', array($extractConfig['excluded_dirs']));
            }

            if (isset($extractConfig['excluded_names'])) {
                $def->addMethodCall('setExcludedNames', array($extractConfig['excluded_names']));
            }

            if (isset($extractConfig['output_format'])) {
                $def->addMethodCall('setOutputFormat', array($extractConfig['output_format']));
            }

            if (isset($extractConfig['default_output_format'])) {
                $def->addMethodCall('setDefaultOutputFormat', array($extractConfig['default_output_format']));
            }

            if (isset($extractConfig['keep'])) {
                $def->addMethodCall('setKeepOldTranslations', array($extractConfig['keep']));
            }

            if (isset($extractConfig['external_translations_dirs'])) {
                $def->addMethodCall('setLoadResources', array($extractConfig['external_translations_dirs']));
            }

            $requests[$name] = $def;
        }

        $container
            ->getDefinition('jms_translation.config_factory')
            ->addArgument($requests)
        ;
    }
}
