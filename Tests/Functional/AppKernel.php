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

namespace JMS\TranslationBundle\Tests\Functional;

use JMS\TranslationBundle\JMSTranslationBundle;
use JMS\TranslationBundle\Tests\Functional\Fixture\TestBundle\TestBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct(
        private string $frameworkConfig,
        private string|null $config = null
    ) {
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return [
            new TestBundle(),
            new FrameworkBundle(),
            new TwigBundle(),
            new JMSTranslationBundle(),
        ];
    }

    private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $configDir = $this->getConfigDir();

        $container->import($configDir . '/' . $this->frameworkConfig);

        if (null !== $this->config) {
            $loader->load($configDir . '/' . $this->config);
        }

        $container->import($configDir . '/services.yaml');
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $configDir = $this->getConfigDir();

        $routes->import($configDir . '/routes.yaml');
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir() . '/cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir() . '/logs';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/JMSTranslationBundle';
    }

    public function __serialize(): array
    {
        return [
            'framework_config' => $this->frameworkConfig,
            'config' => $this->config,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->__construct($data['framework_config'], $data['config']);
    }
}
