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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('jms_translation.twig_extension.class', 'JMS\TranslationBundle\Twig\TranslationExtension')
        ->set('jms_translation.controller.translate_controller.class', 'JMS\TranslationBundle\Controller\TranslateController')
        ->set('jms_translation.controller.api_controller.class', 'JMS\TranslationBundle\Controller\ApiController')
        ->set('jms_translation.extractor_manager.class', 'JMS\TranslationBundle\Translation\ExtractorManager')
        ->set('jms_translation.extractor.file_extractor.class', 'JMS\TranslationBundle\Translation\Extractor\FileExtractor')
        ->set('jms_translation.extractor.file.default_php_extractor', 'JMS\TranslationBundle\Translation\Extractor\File\DefaultPhpFileExtractor')
        ->set('jms_translation.extractor.file.translation_container_extractor', 'JMS\TranslationBundle\Translation\Extractor\File\TranslationContainerExtractor')
        ->set('jms_translation.extractor.file.twig_extractor', 'JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor')
        ->set('jms_translation.extractor.file.form_extractor.class', 'JMS\TranslationBundle\Translation\Extractor\File\FormExtractor')
        ->set('jms_translation.extractor.file.validation_extractor.class', 'JMS\TranslationBundle\Translation\Extractor\File\ValidationExtractor')
        ->set('jms_translation.extractor.file.authentication_message_extractor.class', 'JMS\TranslationBundle\Translation\Extractor\File\AuthenticationMessagesExtractor')
        ->set('jms_translation.loader.symfony.xliff_loader.class', 'JMS\TranslationBundle\Translation\Loader\Symfony\XliffLoader')
        ->set('jms_translation.loader.xliff_loader.class', 'JMS\TranslationBundle\Translation\Loader\XliffLoader')
        ->set('jms_translation.loader.symfony_adapter.class', 'JMS\TranslationBundle\Translation\Loader\SymfonyLoaderAdapter')
        ->set('jms_translation.loader_manager.class', 'JMS\TranslationBundle\Translation\LoaderManager')
        ->set('jms_translation.dumper.php_dumper.class', 'JMS\TranslationBundle\Translation\Dumper\PhpDumper')
        ->set('jms_translation.dumper.xliff_dumper.class', 'JMS\TranslationBundle\Translation\Dumper\XliffDumper')
        ->set('jms_translation.dumper.yaml_dumper.class', 'JMS\TranslationBundle\Translation\Dumper\YamlDumper')
        ->set('jms_translation.dumper.symfony_adapter.class', 'JMS\TranslationBundle\Translation\Dumper\SymfonyDumperAdapter')
        ->set('jms_translation.file_writer.class', 'JMS\TranslationBundle\Translation\FileWriter')
        ->set('jms_translation.updater.class', 'JMS\TranslationBundle\Translation\Updater')
        ->set('jms_translation.config_factory.class', 'JMS\TranslationBundle\Translation\ConfigFactory')
        ->set('jms_translation.file_source_factory.class', 'JMS\TranslationBundle\Translation\FileSourceFactory');

    $services = $container->services();

    $services->set('jms_translation.controller.translate_controller', '%jms_translation.controller.translate_controller.class%')
        ->public()
        ->args([
            service('jms_translation.config_factory'),
            service('jms_translation.loader_manager'),
            service('twig'),
        ])
        ->call('setSourceLanguage', ['%jms_translation.source_language%']);

    $services->alias('JMS\TranslationBundle\Controller\TranslateController', 'jms_translation.controller.translate_controller')
        ->public();

    $services->set('jms_translation.controller.api_controller', '%jms_translation.controller.api_controller.class%')
        ->public()
        ->args([
            service('jms_translation.config_factory'),
            service('jms_translation.updater'),
        ]);

    $services->alias('JMS\TranslationBundle\Controller\ApiController', 'jms_translation.controller.api_controller')
        ->public();

    $services->set('jms_translation.updater', '%jms_translation.updater.class%')
        ->public()
        ->args([
            service('jms_translation.loader_manager'),
            service('jms_translation.extractor_manager'),
            service('logger'),
            service('jms_translation.file_writer'),
        ]);

    $services->set('jms_translation.config_factory', '%jms_translation.config_factory.class%')
        ->public();

    $services->set('jms_translation.file_source_factory', '%jms_translation.file_source_factory.class%')
        ->args([
            '%kernel.project_dir%',
            '%kernel.project_dir%',
        ]);

    $services->set('jms_translation.file_writer', '%jms_translation.file_writer.class%');

    // Loaders
    $services->set('jms_translation.loader.symfony_adapter', '%jms_translation.loader.symfony_adapter.class%')
        ->abstract();

    $services->set('jms_translation.loader_manager', '%jms_translation.loader_manager.class%');

    $services->set('jms_translation.loader.xliff_loader', '%jms_translation.loader.xliff_loader.class%')
        ->tag('jms_translation.loader', ['format' => 'xliff']);

    // Dumpers
    $services->set('jms_translation.dumper.php_dumper', '%jms_translation.dumper.php_dumper.class%')
        ->tag('jms_translation.dumper', ['format' => 'php']);

    $services->set('jms_translation.dumper.xliff_dumper', '%jms_translation.dumper.xliff_dumper.class%')
        ->call('setSourceLanguage', ['%jms_translation.source_language%'])
        ->call('setAddDate', ['%jms_translation.dumper.add_date%'])
        ->call('setAddReference', ['%jms_translation.dumper.add_references%'])
        ->tag('jms_translation.dumper', ['format' => 'xliff']);

    $services->set('jms_translation.dumper.xlf_dumper', '%jms_translation.dumper.xliff_dumper.class%')
        ->call('setSourceLanguage', ['%jms_translation.source_language%'])
        ->call('setAddDate', ['%jms_translation.dumper.add_date%'])
        ->call('setAddReference', ['%jms_translation.dumper.add_references%'])
        ->tag('jms_translation.dumper', ['format' => 'xlf']);

    $services->set('jms_translation.dumper.yaml_dumper', '%jms_translation.dumper.yaml_dumper.class%')
        ->tag('jms_translation.dumper', ['format' => 'yml']);

    $services->set('jms_translation.dumper.symfony_adapter', '%jms_translation.dumper.symfony_adapter.class%')
        ->abstract();

    // Extractors
    $services->set('jms_translation.extractor_manager', '%jms_translation.extractor_manager.class%')
        ->args([
            service('jms_translation.extractor.file_extractor'),
            service('logger'),
        ]);

    // File-based extractors
    $services->set('jms_translation.extractor.file_extractor', '%jms_translation.extractor.file_extractor.class%')
        ->args([
            service('twig'),
            service('logger'),
        ]);

    $services->set('jms_translation.extractor.file.default_php_extractor', '%jms_translation.extractor.file.default_php_extractor%')
        ->args([
            service('jms_translation.doc_parser'),
            service('jms_translation.file_source_factory'),
        ])
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.form_extractor', '%jms_translation.extractor.file.form_extractor.class%')
        ->args([
            service('jms_translation.doc_parser'),
            service('jms_translation.file_source_factory'),
        ])
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.translation_container_extractor', '%jms_translation.extractor.file.translation_container_extractor%')
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.twig_extractor', '%jms_translation.extractor.file.twig_extractor%')
        ->args([
            service('twig'),
            service('jms_translation.file_source_factory'),
        ])
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.validation_extractor', '%jms_translation.extractor.file.validation_extractor.class%')
        ->args([
            service('validator.mapping.class_metadata_factory'),
        ])
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.authentication_message_extractor', '%jms_translation.extractor.file.authentication_message_extractor.class%')
        ->args([
            service('jms_translation.doc_parser'),
            service('jms_translation.file_source_factory'),
        ])
        ->tag('jms_translation.file_visitor');

    // Util
    $services->set('jms_translation.doc_parser', 'Doctrine\Common\Annotations\DocParser')
        ->call('setImports', [[
            'desc' => 'JMS\TranslationBundle\Annotation\Desc',
            'meaning' => 'JMS\TranslationBundle\Annotation\Meaning',
            'ignore' => 'JMS\TranslationBundle\Annotation\Ignore',
        ]])
        ->call('setIgnoreNotImportedAnnotations', [true]);

    $services->set('jms_translation.twig_extension', '%jms_translation.twig_extension.class%')
        ->public()
        ->args([
            service('translator'),
            '%kernel.debug%',
        ])
        ->tag('twig.extension');
};
