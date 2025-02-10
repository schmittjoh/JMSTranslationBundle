<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JMS\TranslationBundle\Translation\Dumper\PhpDumper;
use JMS\TranslationBundle\Translation\Dumper\SymfonyDumperAdapter;
use JMS\TranslationBundle\Translation\Dumper\XliffDumper;
use JMS\TranslationBundle\Translation\Dumper\YamlDumper;
use JMS\TranslationBundle\Translation\Loader\SymfonyLoaderAdapter;
use JMS\TranslationBundle\Translation\Loader\XliffLoader;
use JMS\TranslationBundle\Translation\LoaderManager;
use JMS\TranslationBundle\Translation\Updater;

return static function (ContainerConfigurator $container) {
    $container->services()
        // Loaders
        ->set('jms_translation.loader.symfony_adapter', SymfonyLoaderAdapter::class)
            ->abstract()

        ->set('jms_translation.loader.xliff_loader', XliffLoader::class)
            ->tag('jms_translation.loader', ['format' => 'xliff'])

        ->set('jms_translation.loader_manager', LoaderManager::class)


        // Dumpers
        ->set('jms_translation.dumper.php_dumper', PhpDumper::class)
            ->tag('jms_translation.dumper', ['format' => 'php'])

        ->set('jms_translation.dumper.xliff_dumper', XliffDumper::class)
            ->call('setSourceLanguage', [param('jms_translation.source_language')])
            ->call('setAddDate', [param('jms_translation.dumper.add_date')])
            ->call('setAddReference', [param('jms_translation.dumper.add_references')])
            ->tag('jms_translation.dumper', ['format' => 'xliff'])

        ->set('jms_translation.dumper.xlf_dumper', XliffDumper::class)
            ->call('setSourceLanguage', [param('jms_translation.source_language')])
            ->call('setAddDate', [param('jms_translation.dumper.add_date')])
            ->call('setAddReference', [param('jms_translation.dumper.add_references')])
            ->tag('jms_translation.dumper', ['format' => 'xlf'])

        ->set('jms_translation.dumper.yaml_dumper', YamlDumper::class)
            ->tag('jms_translation.dumper', ['format' => 'yml'])

        ->set('jms_translation.dumper.symfony_adapter', SymfonyDumperAdapter::class)
            ->abstract()


        ->set('jms_translation.updater', Updater::class)
            ->args([
                service('jms_translation.loader_manager'),
                service('jms_translation.extractor_manager'),
                service('jms_translation.file_writer'),
                service('logger'),
            ])
        ->alias(Updater::class, 'jms_translation.updater')
    ;
};

