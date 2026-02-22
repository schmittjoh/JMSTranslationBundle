<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('jms_translation.twig_extension.class', \JMS\TranslationBundle\Twig\TranslationExtension::class);
    $parameters->set('jms_translation.controller.translate_controller.class', \JMS\TranslationBundle\Controller\TranslateController::class);
    $parameters->set('jms_translation.controller.api_controller.class', \JMS\TranslationBundle\Controller\ApiController::class);
    $parameters->set('jms_translation.extractor_manager.class', \JMS\TranslationBundle\Translation\ExtractorManager::class);
    $parameters->set('jms_translation.extractor.file_extractor.class', \JMS\TranslationBundle\Translation\Extractor\FileExtractor::class);
    $parameters->set('jms_translation.extractor.file.default_php_extractor', \JMS\TranslationBundle\Translation\Extractor\File\DefaultPhpFileExtractor::class);
    $parameters->set('jms_translation.extractor.file.translation_container_extractor', \JMS\TranslationBundle\Translation\Extractor\File\TranslationContainerExtractor::class);
    $parameters->set('jms_translation.extractor.file.twig_extractor', \JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor::class);
    $parameters->set('jms_translation.extractor.file.form_extractor.class', \JMS\TranslationBundle\Translation\Extractor\File\FormExtractor::class);
    $parameters->set('jms_translation.extractor.file.validation_extractor.class', \JMS\TranslationBundle\Translation\Extractor\File\ValidationExtractor::class);
    $parameters->set('jms_translation.extractor.file.authentication_message_extractor.class', \JMS\TranslationBundle\Translation\Extractor\File\AuthenticationMessagesExtractor::class);
    $parameters->set('jms_translation.loader.symfony.xliff_loader.class', \JMS\TranslationBundle\Translation\Loader\Symfony\XliffLoader::class);
    $parameters->set('jms_translation.loader.xliff_loader.class', \JMS\TranslationBundle\Translation\Loader\XliffLoader::class);
    $parameters->set('jms_translation.loader.symfony_adapter.class', \JMS\TranslationBundle\Translation\Loader\SymfonyLoaderAdapter::class);
    $parameters->set('jms_translation.loader_manager.class', \JMS\TranslationBundle\Translation\LoaderManager::class);
    $parameters->set('jms_translation.dumper.php_dumper.class', \JMS\TranslationBundle\Translation\Dumper\PhpDumper::class);
    $parameters->set('jms_translation.dumper.xliff_dumper.class', \JMS\TranslationBundle\Translation\Dumper\XliffDumper::class);
    $parameters->set('jms_translation.dumper.yaml_dumper.class', \JMS\TranslationBundle\Translation\Dumper\YamlDumper::class);
    $parameters->set('jms_translation.dumper.symfony_adapter.class', \JMS\TranslationBundle\Translation\Dumper\SymfonyDumperAdapter::class);
    $parameters->set('jms_translation.file_writer.class', \JMS\TranslationBundle\Translation\FileWriter::class);
    $parameters->set('jms_translation.updater.class', \JMS\TranslationBundle\Translation\Updater::class);
    $parameters->set('jms_translation.config_factory.class', \JMS\TranslationBundle\Translation\ConfigFactory::class);
    $parameters->set('jms_translation.file_source_factory.class', \JMS\TranslationBundle\Translation\FileSourceFactory::class);

    $services->set('jms_translation.controller.translate_controller', '%jms_translation.controller.translate_controller.class%')
        ->public()
        ->args([
            service('jms_translation.config_factory'),
            service('jms_translation.loader_manager'),
            service('twig'),
        ])
        ->call('setSourceLanguage', ['%jms_translation.source_language%']);

    $services->alias(\JMS\TranslationBundle\Controller\TranslateController::class, 'jms_translation.controller.translate_controller')
        ->public();

    $services->set('jms_translation.controller.api_controller', '%jms_translation.controller.api_controller.class%')
        ->public()
        ->args([
            service('jms_translation.config_factory'),
            service('jms_translation.updater'),
        ]);

    $services->alias(\JMS\TranslationBundle\Controller\ApiController::class, 'jms_translation.controller.api_controller')
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

    $services->set('jms_translation.file_writer', '%jms_translation.file_writer.class%')
        ->private();

    $services->set('jms_translation.loader.symfony_adapter', '%jms_translation.loader.symfony_adapter.class%')
        ->private()
        ->abstract();

    $services->set('jms_translation.loader_manager', '%jms_translation.loader_manager.class%');

    $services->set('jms_translation.loader.xliff_loader', '%jms_translation.loader.xliff_loader.class%')
        ->private()
        ->tag('jms_translation.loader', ['format' => 'xliff']);

    $services->set('jms_translation.dumper.php_dumper', '%jms_translation.dumper.php_dumper.class%')
        ->private()
        ->tag('jms_translation.dumper', ['format' => 'php']);

    $services->set('jms_translation.dumper.xliff_dumper', '%jms_translation.dumper.xliff_dumper.class%')
        ->private()
        ->call('setSourceLanguage', ['%jms_translation.source_language%'])
        ->call('setAddDate', ['%jms_translation.dumper.add_date%'])
        ->call('setAddReference', ['%jms_translation.dumper.add_references%'])
        ->tag('jms_translation.dumper', ['format' => 'xliff']);

    $services->set('jms_translation.dumper.xlf_dumper', '%jms_translation.dumper.xliff_dumper.class%')
        ->private()
        ->call('setSourceLanguage', ['%jms_translation.source_language%'])
        ->call('setAddDate', ['%jms_translation.dumper.add_date%'])
        ->call('setAddReference', ['%jms_translation.dumper.add_references%'])
        ->tag('jms_translation.dumper', ['format' => 'xlf']);

    $services->set('jms_translation.dumper.yaml_dumper', '%jms_translation.dumper.yaml_dumper.class%')
        ->private()
        ->tag('jms_translation.dumper', ['format' => 'yml']);

    $services->set('jms_translation.dumper.symfony_adapter', '%jms_translation.dumper.symfony_adapter.class%')
        ->private()
        ->abstract();

    $services->set('jms_translation.extractor_manager', '%jms_translation.extractor_manager.class%')
        ->private()
        ->args([
            service('jms_translation.extractor.file_extractor'),
            service('logger'),
        ]);

    $services->set('jms_translation.extractor.file_extractor', '%jms_translation.extractor.file_extractor.class%')
        ->private()
        ->args([
            service('twig'),
            service('logger'),
        ]);

    $services->set('jms_translation.extractor.file.default_php_extractor', '%jms_translation.extractor.file.default_php_extractor%')
        ->private()
        ->args([
            service('jms_translation.doc_parser'),
            service('jms_translation.file_source_factory'),
        ])
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.form_extractor', '%jms_translation.extractor.file.form_extractor.class%')
        ->private()
        ->args([
            service('jms_translation.doc_parser'),
            service('jms_translation.file_source_factory'),
        ])
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.translation_container_extractor', '%jms_translation.extractor.file.translation_container_extractor%')
        ->private()
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.twig_extractor', '%jms_translation.extractor.file.twig_extractor%')
        ->private()
        ->args([
            service('twig'),
            service('jms_translation.file_source_factory'),
        ])
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.validation_extractor', '%jms_translation.extractor.file.validation_extractor.class%')
        ->private()
        ->args([service('validator.mapping.class_metadata_factory')])
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.extractor.file.authentication_message_extractor', '%jms_translation.extractor.file.authentication_message_extractor.class%')
        ->private()
        ->args([
            service('jms_translation.doc_parser'),
            service('jms_translation.file_source_factory'),
        ])
        ->tag('jms_translation.file_visitor');

    $services->set('jms_translation.doc_parser', \Doctrine\Common\Annotations\DocParser::class)
        ->private()
        ->call('setImports', [['desc' => \JMS\TranslationBundle\Annotation\Desc::class, 'meaning' => \JMS\TranslationBundle\Annotation\Meaning::class, 'ignore' => \JMS\TranslationBundle\Annotation\Ignore::class]])
        ->call('setIgnoreNotImportedAnnotations', [true]);

    $services->set('jms_translation.twig_extension', '%jms_translation.twig_extension.class%')
        ->public()
        ->args([
            service('translator'),
            '%kernel.debug%',
        ])
        ->tag('twig.extension');
};
