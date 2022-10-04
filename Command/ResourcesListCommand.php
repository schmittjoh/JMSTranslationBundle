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

namespace JMS\TranslationBundle\Command;

use JMS\TranslationBundle\Util\FileUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class ResourcesListCommand extends Command
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string|null
     */
    private $rootDir;

    /**
     * @var array
     */
    private $bundles;

    public function __construct(string $projectDir, array $bundles, ?string $rootDir)
    {
        $this->projectDir = $projectDir;
        $this->bundles = $bundles;
        $this->rootDir = $rootDir;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('translation:list-resources')
            ->setDescription('List translation resources available.')
            ->addOption('files', null, InputOption::VALUE_OPTIONAL, 'Display only files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directoriesToSearch = [];

        // TODO: Remove this block when dropping support of Symfony 4 as it will always be false
        if ($this->rootDir !== null) {
            $directoriesToSearch[] = realpath($this->rootDir);
        }

        $basePath = realpath($this->projectDir);

        $directoriesToSearch[] = $basePath;

        $dirs = $this->retrieveDirs();

        if (!$input->hasParameterOption('--files')) {
            $output->writeln('<info>Directories list :</info>');
            foreach ($dirs as $dir) {
                $path = str_replace($directoriesToSearch, ['%kernel.root_dir%', '%kernel.project_dir%'], $dir);
                $output->writeln(sprintf('    - %s', $path));
            }

            $output->writeln('done!');

            return 0;
        }

        $output->writeln('<info>Resources list :</info>');

        $files = $this->retrieveFiles($dirs);

        foreach ($files as $file) {
            $path = str_replace($basePath, '%kernel.project_dir%', (string) $file);
            $output->writeln(sprintf('    - %s', $path));
        }

        $output->writeln('done!');

        return 0;
    }

    /**
     * @param array $dirs
     *
     * @return array
     */
    private function retrieveFiles(array $dirs)
    {
        $files = [];
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
        $dirs = [];
        foreach ($this->bundles as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            if (is_dir($dir = dirname($reflection->getFilename()) . '/Resources/translations')) {
                $dirs[] = $dir;
            }
        }

        // TODO: Remove this block when dropping support of Symfony 4
        if (
            $this->rootDir !== null &&
            is_dir($dir = $this->rootDir . '/Resources/translations')
        ) {
            $dirs[] = $dir;
        }

        if (is_dir($dir = $this->projectDir . '/translations')) {
            $dirs[] = $dir;
        }

        return $dirs;
    }
}
