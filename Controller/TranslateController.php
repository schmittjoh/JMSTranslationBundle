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

namespace JMS\TranslationBundle\Controller;

use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Exception\InvalidArgumentException;
use Symfony\Component\Translation\MessageCatalogue;

use JMS\TranslationBundle\Util\FileUtils;

use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Translate Controller.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class TranslateController
{
    /** @DI\Inject */
    private $request;

    /** @DI\Inject("jms_translation.config_factory") */
    private $configFactory;

    /** @DI\Inject("translation.loader") */
    private $loader;

    /** @DI\Inject("service_container") */
    private $container;

    /**
     * @Route("/", name="jms_translation_index", options = {"i18n" = false})
     * @Template
     * @param string $config
     */
    public function indexAction()
    {
        $configs = $this->configFactory->getNames();
        $config = $this->request->query->get('config') ?: reset($configs);
        if (!$config) {
            throw new RuntimeException('You need to configure at least one config under "jms_translation.configs".');
        }

        $translationsDir = $this->configFactory->getConfig($config, 'en')->getTranslationsDir();
        $files = FileUtils::findTranslationFiles($translationsDir);
        if (empty($files)) {
            throw new RuntimeException('There are no translation files for this config, please run the translation:extract command first.');
        }

        $domains = array_keys($files);
        $domain = $this->request->query->get('domain') ?: reset($domains);
        if ((!$domain = $this->request->query->get('domain')) || !isset($files[$domain])) {
            $domain = reset($domains);
        }

        $locales = array_keys($files[$domain]);
        if ((!$locale = $this->request->query->get('locale')) || !isset($files[$domain][$locale])) {
            $locale = reset($locales);
        }

        $loader = $this->getLoader($files[$domain][$locale][0]);
        $catalogue = $loader->load($files[$domain][$locale][1]->getPathName(), $locale, $domain);

        $messages = $catalogue->all($domain);
        $messagesCopy = $messages; // necessary otherwise we get concurrent modifications
        uksort($messages, function($a, $b) use ($messagesCopy) {
            if ($a === $messagesCopy[$a]) {
                return -1;
            }

            if ($b === $messagesCopy[$b]) {
                return 1;
            }

            return 0;
        });

        $alternativeMessages = array();
        foreach ($locales as $otherLocale) {
            if ($locale === $otherLocale) {
                continue;
            }

            $loader = $this->getLoader($files[$domain][$otherLocale][0]);
            $catalogue = $loader->load($files[$domain][$otherLocale][1]->getPathName(), $otherLocale, $domain);

            foreach ($catalogue->all($domain) as $id => $message) {
                $alternativeMessages[$id][$otherLocale] = $message;
            }
        }

        return array(
            'selectedConfig' => $config,
            'configs' => $configs,
            'selectedDomain' => $domain,
            'domains' => $domains,
            'selectedLocale' => $locale,
            'locales' => $locales,
            'format' => $files[$domain][$locale][0],
            'messages' => $messages,
            'alternativeMessages' => $alternativeMessages,
            'isWriteable' => is_writeable($files[$domain][$locale][1]),
            'file' => (string) $files[$domain][$locale][1],
        );
    }

    protected function getLoader($format)
    {
        // This isn't exactly clean, but Symfony does not provide any other way atm
        $loaderId = sprintf('translation.loader.%s', $format);

        if (!$this->container->has($loaderId)) {
            throw new InvalidArgumentException(sprintf('There is no loader for format "%s".', $format));
        }

        return $this->container->get($loaderId);
    }
}