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

namespace JMS\TranslationBundle\Twig;

/**
 * Provides some extensions for specifying translation metadata.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
use Symfony\Component\Translation\TranslatorInterface;

class TranslationExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var bool
     */
    private $debug;

    /**
     * TranslationExtension constructor.
     * @param TranslatorInterface $translator
     * @param bool $debug
     */
    public function __construct(TranslatorInterface $translator, $debug = false)
    {
        $this->translator = $translator;
        $this->debug = $debug;
    }

    /**
     * @return array
     */
    public function getNodeVisitors()
    {
        $visitors = array(
            new NormalizingNodeVisitor(),
            new RemovingNodeVisitor(),
        );

        if ($this->debug) {
            $visitors[] = new DefaultApplyingNodeVisitor();
        }

        return $visitors;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('desc', array($this, 'desc')),
            new \Twig_SimpleFilter('meaning', array($this, 'meaning')),
        );
    }

    /**
     * @param string $message
     * @param string $defaultMessage
     * @param int $count
     * @param array $arguments
     * @param null|string $domain
     * @param null|string $locale
     * @return string
     */
    public function transchoiceWithDefault($message, $defaultMessage, $count, array $arguments = array(), $domain = null, $locale = null)
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        // If < sf2.6
        if (!method_exists($this->translator, 'getCatalogue')) {
            return $this->transchoiceWithDefaultLegacy($message, $defaultMessage, $count, $arguments, $domain, $locale);
        }

        if (false == $this->translator->getCatalogue($locale)->defines($message, $domain)) {
            return $this->translator->transChoice($defaultMessage, $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale);
        }

        return $this->translator->transChoice($message, $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale);
    }

    /**
     * @param $v
     * @return mixed
     */
    public function desc($v)
    {
        return $v;
    }

    /**
     * @param $v
     * @return mixed
     */
    public function meaning($v)
    {
        return $v;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jms_translation';
    }

    /**
     * This function exists to support Symfony 2.3
     *
     * @param string $message
     * @param string $defaultMessage
     * @param int $count
     * @param array $arguments
     * @param string $domain
     * @param string $locale
     *
     * @return string
     */
    private function transchoiceWithDefaultLegacy($message, $defaultMessage, $count, array $arguments, $domain, $locale)
    {
        try {
            $translatedMessage = $this->translator->transChoice($message, $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale);

            if ($translatedMessage !== $message) {
                return $translatedMessage;
            }
        } catch (\InvalidArgumentException $e) {
        }

        return $this->translator->transChoice($defaultMessage, $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale);
    }
}
