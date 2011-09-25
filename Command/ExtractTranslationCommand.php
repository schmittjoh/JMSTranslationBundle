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

use JMS\TranslationBundle\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use JMS\TranslationBundle\Translation\UpdateRequest;
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
            ->addArgument('locale', InputArgument::REQUIRED, 'The locale for which to extract messages.')
            ->addOption('enable-extractor', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The alias of an extractor which should be enabled.')
            ->addOption('disable-extractor', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The alias of an extractor which should be disabled (only required for overriding config values).')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'The config to use')
            ->addOption('bundle', 'b', InputOption::VALUE_REQUIRED, 'The bundle that you want to extract translations for.')
            ->addOption('exclude-name', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'A pattern which should be ignored, e.g. *Test.php')
            ->addOption('exclude-dir', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'A directory name which should be ignored, e.g. Tests')
            ->addOption('ignore-domain', 'i', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'A domain to ignore.')
            ->addOption('dir', 'd', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'A directory to scan for messages.')
            ->addOption('output-dir', null, InputOption::VALUE_REQUIRED, 'The directory where files should be written to.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'When specified, changes are _NOT_ persisted to disk.')
            ->addOption('output-format', null, InputOption::VALUE_REQUIRED, 'The output format that should be used (in most cases, it is better to change only the default-output-format).')
            ->addOption('default-output-format', null, InputOption::VALUE_REQUIRED, 'The default output format (defaults to yml).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = $input->getOption('config') ?
                       $this->getContainer()->get('jms_translation.update_request_factory')->getRequest($input->getOption('config'))
                       : new UpdateRequest();

        $this->updateRequestWithInput($input, $request);

        $output->writeln(sprintf('Output-Path: <info>%s</info>', $request->getTranslationsDir()));
        $output->writeln(sprintf('Directories: <info>%s</info>', implode(', ', $request->getScanDirs())));
        $output->writeln(sprintf('Excluded Directories: <info>%s</info>', $request->getExcludedDirs() ? implode(', ', $request->getExcludedDirs()) : '# none #'));
        $output->writeln(sprintf('Excluded Names: <info>%s</info>', $request->getExcludedNames() ? implode(', ', $request->getExcludedNames()) : '# none #'));
        $output->writeln(sprintf('Output-Format: <info>%s</info>', $request->getOutputFormat() ? $request->getOutputFormat() : '# whatever is present, if nothing then '.$request->getDefaultOutputFormat().' #'));
        $output->writeln(sprintf('Custom Extractors: <info>%s</info>', $request->getEnabledExtractors() ? implode(', ', array_keys($request->getEnabledExtractors())) : '# none #'));
        $output->writeln('============================================================');

        $updater = $this->getContainer()->get('jms_translation.updater');
        $updater->setLogger($logger = new OutputLogger($output));

        if (!$input->getOption('verbose')) {
            $logger->setLevel(OutputLogger::ALL ^ OutputLogger::DEBUG);
        }

        if ($input->getOption('dry-run')) {
            $changeSet = $updater->getChangeSet($request);

            $output->writeln('Added Messages: '.implode(', ', array_keys($changeSet->getAddedMessages())));
            $output->writeln('Deleted Messages: '.implode(', ', array_keys($changeSet->getDeletedMessages())));

            return;
        }

        $updater->process($request);
    }

    private function updateRequestWithInput(InputInterface $input, UpdateRequest $request)
    {
        if ($bundle = $input->getOption('bundle')) {
            if ('@' === $bundle[0]) {
                $bundle = substr($bundle, 1);
            }

            $bundle = $this->getApplication()->getKernel()->getBundle($bundle);
            $request->setTranslationsDir($bundle->getPath().'/Resources/translations');
            $request->setScanDirs(array($bundle->getPath()));
        }

        if (!$dirs = $input->getOption('dir')) {
            if (!$request->getScanDirs()) {
                throw new RuntimeException('You must pass at least one directory which should be scanned via "--dir" or "--config".');
            }
        } else {
            $request->setScanDirs($dirs);
        }

        if (!$outputDir = $input->getOption('output-dir')) {
            if (!$request->getTranslationsDir()) {
                throw new RuntimeException('You must pass the output directory via "--output-dir" or "--config".');
            }
        } else {
            $request->setTranslationsDir($outputDir);
        }

        if ($outputFormat = $input->getOption('output-format')) {
            $request->setOutputFormat($outputFormat);
        }

        if ($input->getOption('ignore-domain')) {
            $ignored = array();
            foreach ($input->getOption('ignore-domain') as $domain) {
                $ignored[$domain] = true;
            }
            $request->setIgnoredDomains($ignored);
        }

        if ($excludeDirs = $input->getOption('exclude-dir')) {
            $request->setExcludedDirs($excludeDirs);
        }

        if ($excludeNames = $input->getOption('exclude-name')) {
            $request->setExcludedNames($excludeNames);
        }

        if ($format = $input->getOption('default-output-format')) {
            $request->setDefaultOutputFormat($format);
        }

        if ($enabledExtractors = $input->getOption('enable-extractor')) {
            $enabled = $request->getEnabledExtractors();
            foreach ($enabledExtractors as $alias) {
                $enabled[$alias] = true;
            }

            $request->setEnabledExtractors($enabled);
        }

        if ($disabledExtractors = $input->getOption('disable-extractor')) {
            $enabled = $request->getEnabledExtractors();
            foreach ($disabledExtractors as $alias) {
                unset($enabled[$alias]);
            }

            $request->setEnabledExtractors($enabled);
        }

        $request->setLocale($input->getArgument('locale'));
    }
}