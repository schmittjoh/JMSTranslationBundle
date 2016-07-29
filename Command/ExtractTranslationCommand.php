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
use Symfony\Component\Console\Input\InputArgument;
use JMS\TranslationBundle\Translation\Config;
use JMS\TranslationBundle\Logger\OutputLogger;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Command for extracting translations.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ExtractTranslationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('translation:extract')
            ->setDescription('Extracts translation messages from your code.')
            ->addArgument('locales', InputArgument::IS_ARRAY, 'The locales for which to extract messages.')
            ->addOption('enable-extractor', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The alias of an extractor which should be enabled.')
            ->addOption('disable-extractor', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The alias of an extractor which should be disabled (only required for overriding config values).')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'The config to use')
            ->addOption('bundle', 'b', InputOption::VALUE_REQUIRED, 'The bundle that you want to extract translations for.')
            ->addOption('exclude-name', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'A pattern which should be ignored, e.g. *Test.php')
            ->addOption('exclude-dir', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'A directory name which should be ignored, e.g. Tests')
            ->addOption('ignore-domain', 'i', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'A domain to ignore.')
            ->addOption('domain', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Use only this domain.')
            ->addOption('dir', 'd', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'A directory to scan for messages.')
            ->addOption('output-dir', null, InputOption::VALUE_REQUIRED, 'The directory where files should be written to.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'When specified, changes are _NOT_ persisted to disk.')
            ->addOption('output-format', null, InputOption::VALUE_REQUIRED, 'The output format that should be used (in most cases, it is better to change only the default-output-format).')
            ->addOption('default-output-format', null, InputOption::VALUE_REQUIRED, 'The default output format (defaults to xliff).')
            ->addOption('keep', null, InputOption::VALUE_NONE, 'Define if the updater service should keep the old translation (defaults to false).')
            ->addOption('external-translations-dir', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED , 'Load external translation ressources')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $builder = $input->getOption('config') ?
                       $this->getContainer()->get('jms_translation.config_factory')->getBuilder($input->getOption('config'))
                       : new ConfigBuilder();

        $this->updateWithInput($input, $builder);

        $locales = $input->getArgument('locales');
        if (empty($locales)) {
            $locales = $this->getContainer()->getParameter('jms_translation.locales');
        }

        if (empty($locales)) {
            throw new \LogicException('No locales were given, and no locales are configured.');
        }

        foreach ($locales as $locale) {
            $config = $builder->setLocale($locale)->getConfig();

            $output->writeln(sprintf('Extracting Translations for locale <info>%s</info>', $locale));
            $output->writeln(sprintf('Keep old translations: <info>%s</info>', $config->isKeepOldMessages() ? 'Yes' : 'No'));
            $output->writeln(sprintf('Output-Path: <info>%s</info>', $config->getTranslationsDir()));
            $output->writeln(sprintf('Directories: <info>%s</info>', implode(', ', $config->getScanDirs())));
            $output->writeln(sprintf('Excluded Directories: <info>%s</info>', $config->getExcludedDirs() ? implode(', ', $config->getExcludedDirs()) : '# none #'));
            $output->writeln(sprintf('Excluded Names: <info>%s</info>', $config->getExcludedNames() ? implode(', ', $config->getExcludedNames()) : '# none #'));
            $output->writeln(sprintf('Output-Format: <info>%s</info>', $config->getOutputFormat() ? $config->getOutputFormat() : '# whatever is present, if nothing then '.$config->getDefaultOutputFormat().' #'));
            $output->writeln(sprintf('Custom Extractors: <info>%s</info>', $config->getEnabledExtractors() ? implode(', ', array_keys($config->getEnabledExtractors())) : '# none #'));
            $output->writeln('============================================================');

            $updater = $this->getContainer()->get('jms_translation.updater');
            $updater->setLogger($logger = new OutputLogger($output));

            if (!$input->getOption('verbose')) {
                $logger->setLevel(OutputLogger::ALL ^ OutputLogger::DEBUG);
            }

            if ($input->getOption('dry-run')) {
                $changeSet = $updater->getChangeSet($config);

                $output->writeln('Added Messages: '.count($changeSet->getAddedMessages()));
                if($input->hasParameterOption('--verbose')){
                    foreach($changeSet->getAddedMessages() as $message){
                        $output->writeln($message->getId(). '-> '.$message->getDesc());
                    }   
                }

                if ($config->isKeepOldMessages()) {
                    $output->writeln('Deleted Messages: # none as "Keep Old Translations" is true #');
                } else {
                    $output->writeln('Deleted Messages: '.count($changeSet->getDeletedMessages()));
                    if($input->hasParameterOption('--verbose')){
                        foreach($changeSet->getDeletedMessages() as $message){
                            $output->writeln($message->getId(). '-> '.$message->getDesc());
                        }   
                    }
                }

                return;
            }

            $updater->process($config);
        }

        $output->writeln('done!');
    }

    private function updateWithInput(InputInterface $input, ConfigBuilder $builder)
    {
        if ($bundle = $input->getOption('bundle')) {
            if ('@' === $bundle[0]) {
                $bundle = substr($bundle, 1);
            }

            $bundle = $this->getApplication()->getKernel()->getBundle($bundle);
            $builder->setTranslationsDir($bundle->getPath().'/Resources/translations');
            $builder->setScanDirs(array($bundle->getPath()));
        }

        if ($dirs = $input->getOption('dir')) {
            $builder->setScanDirs($dirs);
        }

        if ($outputDir = $input->getOption('output-dir')) {
            $builder->setTranslationsDir($outputDir);
        }

        if ($outputFormat = $input->getOption('output-format')) {
            $builder->setOutputFormat($outputFormat);
        }

        if ($input->getOption('ignore-domain')) {
            foreach ($input->getOption('ignore-domain') as $domain) {
                $builder->addIgnoredDomain($domain);
            }
        }

        if ($input->getOption('domain')) {
            foreach ($input->getOption('domain') as $domain) {
                $builder->addDomain($domain);
            }
        }

        if ($excludeDirs = $input->getOption('exclude-dir')) {
            $builder->setExcludedDirs($excludeDirs);
        }

        if ($excludeNames = $input->getOption('exclude-name')) {
            $builder->setExcludedNames($excludeNames);
        }

        if ($format = $input->getOption('default-output-format')) {
            $builder->setDefaultOutputFormat($format);
        }

        if ($enabledExtractors = $input->getOption('enable-extractor')) {
            foreach ($enabledExtractors as $alias) {
                $builder->enableExtractor($alias);
            }
        }

        if ($disabledExtractors = $input->getOption('disable-extractor')) {
            foreach ($disabledExtractors as $alias) {
                $builder->disableExtractor($alias);
            }
        }

        if ($input->hasParameterOption('--keep') || $input->hasParameterOption('--keep=true')) {
            $builder->setKeepOldTranslations(true);
        } else if ($input->hasParameterOption('--keep=false')) {
            $builder->setKeepOldTranslations(false);
        }

        if ($loadResource = $input->getOption('external-translations-dir')) {
            $builder->setLoadResources($loadResource);
        }
    }
}