<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('jms_translation.command.extract', \JMS\TranslationBundle\Command\ExtractTranslationCommand::class)
        ->private()
        ->args([
            service('jms_translation.config_factory'),
            service('jms_translation.updater'),
            '%jms_translation.locales%',
        ])
        ->tag('console.command', ['command' => 'jms:translation:extract']);

    $services->set('jms_translation.command.list_resources', \JMS\TranslationBundle\Command\ResourcesListCommand::class)
        ->private()
        ->args([
            '%kernel.project_dir%',
            '%kernel.bundles%',
        ])
        ->tag('console.command', ['command' => 'jms:translation:list-resources']);
};
