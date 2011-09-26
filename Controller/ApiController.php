<?php

namespace JMS\TranslationBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use JMS\TranslationBundle\Translation\XliffMessageUpdater;

use JMS\TranslationBundle\Util\FileUtils;

use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ApiController
{
    /** @DI\Inject("jms_translation.config_factory") */
    private $configFactory;

    /** @DI\Inject */
    private $request;

    /**
     * @Route("/configs/{config}/domains/{domain}/locales/{locale}/messages/{id}", name="jms_translation_update_message", defaults = {"id" = null})
     * @Method("PUT")
     */
    public function updateMessageAction($config, $domain, $locale, $id)
    {
        $config = $this->configFactory->getConfig($config, $locale);

        $files = FileUtils::findTranslationFiles($config->getTranslationsDir());
        if (!isset($files[$domain][$locale])) {
            throw new \RuntimeException(sprintf('There is no translation file for domain "%s" and locale "%s".', $domain, $locale));
        }

        // TODO: This needs more refactoring, the only sane way I see right now is to replace
        //       the loaders of the translation component as these currently simply discard
        //       the extra information that is contained in these files

        list($format, $file) = $files[$domain][$locale];
        if ('xliff' !== $format) {
            throw new \RuntimeException(sprintf('This is only available for the XLIFF format, but got "%s".', $format));
        }

        // TODO: Do not hard-code this
        $updater = new XliffMessageUpdater();
        $updater->update($file->getPathName(), $id, $this->request->request->get('message'));

        return new Response();
    }
}