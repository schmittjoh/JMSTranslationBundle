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
    $services = $container->services();

    $services->set('jms_translation.command.extract', 'JMS\TranslationBundle\Command\ExtractTranslationCommand')
        ->tag('console.command', ['command' => 'jms:translation:extract'])
        ->args([
            service('jms_translation.config_factory'),
            service('jms_translation.updater'),
            '%jms_translation.locales%',
        ]);

    $services->set('jms_translation.command.list_resources', 'JMS\TranslationBundle\Command\ResourcesListCommand')
        ->tag('console.command', ['command' => 'jms:translation:list-resources'])
        ->args([
            '%kernel.project_dir%',
            '%kernel.bundles%',
        ]);
};
