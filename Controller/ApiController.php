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
use JMS\TranslationBundle\Translation\ConfigFactory;
use JMS\TranslationBundle\Translation\Updater;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\TranslationBundle\Util\FileUtils;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/api")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ApiController
{
    /**
     * @DI\Inject("jms_translation.config_factory")
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @DI\Inject("jms_translation.updater")
     * @var Updater
     */
    private $updater;

    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_NO_TRANSLATION_FILE = 'NO_TRANSLATION_FILE';
    const STATUS_INVALID_INDEX = 'INVALID_INDEX';

    /**
     * @Route("/configs/{config}/domains/{domain}/locales/{locale}/messages",
     *            name="jms_translation_update_message",
     *            defaults = {"id" = null, "type" = "message", "index" = null},
     *            options = {"i18n" = false})
     * @Method("PUT")
     * @param Request $request
     * @param string $config
     * @param string $domain
     * @param string $locale
     *
     * @return JsonResponse
     */
    public function updateMessageAction(Request $request, $config, $domain, $locale)
    {
        $id = $request->query->get('id');
        $type = $request->query->get('type', 'message');

        // Used to build the JSON response
        $responseData = array (
            'status'  => null,
            'message' => null,
        );

        $config = $this->configFactory->getConfig($config, $locale);

        $files = FileUtils::findTranslationFiles($config->getTranslationsDir());
        if (!isset($files[$domain][$locale])) {
            $responseData['message'] = sprintf('There is no translation file for domain "%s" and locale "%s".', $domain, $locale);
            $responseData['status'] = self::STATUS_NO_TRANSLATION_FILE;
            
            $response = new JsonResponse($responseData);
            return $response;
        }

        // TODO: This needs more refactoring, the only sane way I see right now is to replace
        //       the loaders of the translation component as these currently simply discard
        //       the extra information that is contained in these files


        list($format, $file) = $files[$domain][$locale];

        if ($type == 'message') {
            // Update the translated message
            $this->updater->updateTranslation(
                $file, $format, $domain, $locale, $id,
                $request->request->get('message')
            );
            $responseData['status'] = self::STATUS_SUCCESS;
            $responseData['message'] = 'Message was saved.';
        } else if ($type == 'note') {
            // Update the note at the specified index
            $index = $request->query->get('index');
            $message = trim($request->request->get('message'));

            // Use an empty note to signal that it should be deleted
            if (!strlen ($message)) {
                $message = null;
            }

            if (!ctype_digit ($index)) {
                $responseData['status'] = self::STATUS_INVALID_INDEX;
                $responseData['message'] = 'Invalid index specified.';
                return new JsonResponse($responseData);
            }

            $this->updater->updateNote(
                $file, $format, $domain, $locale, $id,
                (int)$index, $message
            );

            $responseData['status'] = self::STATUS_SUCCESS;
            $responseData['message'] = (null == $message) ? 'Note was deleted.' : 'Note was saved.';
        }

        return new JsonResponse($responseData);
    }
}
