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

namespace JMS\TranslationBundle\Command;

use JMS\TranslationBundle\Translation\ConfigBuilder;
use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Translation\Config;
use JMS\TranslationBundle\Logger\OutputLogger;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Finder\Finder;
use JMS\TranslationBundle\Util\FileUtils;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class ResourcesListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('translation:list-resources')
            ->setDescription('List translation resources available.')
            ->addOption('files', null, InputOption::VALUE_OPTIONAL, 'Display only files')
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootPath = realpath($this->getContainer()->getParameter('kernel.root_dir'));
        $basePath = realpath($this->getContainer()->getParameter('kernel.root_dir').'/..');

        $dirs = $this->retrieveDirs();

        if (!$input->hasParameterOption('--files')) {
            $output->writeln('<info>Directories list :</info>');
            foreach ($dirs as $dir) {
                $path = str_replace($rootPath, '%kernel.root_dir%', $dir);
                $path = str_replace($basePath, '%kernel.root_dir%/..', $path);
                $output->writeln(sprintf('    - %s', $path));
            }

            $output->writeln('done!');

            return;
        }

        $output->writeln('<info>Resources list :</info>');

        $files = $this->retrieveFiles($dirs);

        foreach ($files as $file) {
            $path = str_replace($basePath, '%kernel.root_dir%', $file);
            $output->writeln(sprintf(' - %s', $path));
        }

        $output->writeln('done!');
    }

    /**
     * @param array $dirs
     * @return array
     */
    private function retrieveFiles(array $dirs)
    {
        $files = array();
        // Register translation resources
        foreach ($dirs as $dir) {
            foreach (FileUtils::findTranslationFiles($dir) as $catalogue => $locales) {
                foreach ($locales as $file) {
                    $files[] = $file[1];
                }
            }
        }

        return $files;
    }

    /**
     * The following methods is derived from code of the FrameworkExtension.php file from the Symfony2 framework
     *
     * @return array
     */
    private function retrieveDirs()
    {
        // Discover translation directories
        $dirs = array();
        foreach ($this->getContainer()->getParameter('kernel.bundles') as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            if (is_dir($dir = dirname($reflection->getFilename()).'/Resources/translations')) {
                $dirs[] = $dir;
            }
        }

        if (is_dir($dir = $this->getContainer()->getParameter('kernel.root_dir').'/Resources/translations')) {
            $dirs[] = $dir;
        }

        return $dirs;
    }
}
