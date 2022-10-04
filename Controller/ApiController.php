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

namespace JMS\TranslationBundle\Controller;

use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Translation\ConfigFactory;
use JMS\TranslationBundle\Translation\Updater;
use JMS\TranslationBundle\Util\FileUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * @Route("/api")
 */
class ApiController
{
    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @var Updater
     */
    private $updater;

    public function __construct(ConfigFactory $configFactory, Updater $updater)
    {
        $this->configFactory = $configFactory;
        $this->updater = $updater;
    }

    /**
     * @param Request $request
     * @param string $config
     * @param string $domain
     * @param string $locale
     *
     * @return Response
     *
     * @Route("/configs/{config}/domains/{domain}/locales/{locale}/messages",
     *            methods={"PUT"},
     *            name="jms_translation_update_message",
     *            defaults = {"id" = null},
     *            options = {"i18n" = false})
     */
    public function updateMessageAction(Request $request, $config, $domain, $locale)
    {
        $id = $request->query->get('id');

        $config = $this->configFactory->getConfig($config, $locale);

        $files = FileUtils::findTranslationFiles($config->getTranslationsDir());
        if (!isset($files[$domain][$locale])) {
            throw new RuntimeException(sprintf('There is no translation file for domain "%s" and locale "%s".', $domain, $locale));
        }

        // TODO: This needs more refactoring, the only sane way I see right now is to replace
        //       the loaders of the translation component as these currently simply discard
        //       the extra information that is contained in these files

        [$format, $file] = $files[$domain][$locale];

        $this->updater->updateTranslation(
            $file->getPathname(),
            $format,
            $domain,
            $locale,
            $id,
            $request->request->get('message')
        );

        return new Response('Translation was saved');
    }
}
