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

namespace JMS\TranslationBundle\Twig;

/**
 * Provides some extensions for specifying translation metadata.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TranslationExtension extends AbstractExtension
{
    /**
     * @var TranslatorInterface|LegacyTranslatorInterface
     */
    private $translator;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param TranslatorInterface|LegacyTranslatorInterface $translator
     * @param bool $debug
     */
    public function __construct($translator, $debug = false)
    {
        if (!$translator instanceof LegacyTranslatorInterface && !$translator instanceof TranslatorInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 must be an instance of %s or %s, instance of %s given',
                TranslatorInterface::class,
                LegacyTranslatorInterface::class,
                get_class($translator)
            ));
        }

        $this->translator = $translator;
        $this->debug = $debug;
    }

    /**
     * @return array
     */
    public function getNodeVisitors()
    {
        $visitors = [
            new NormalizingNodeVisitor(),
            new RemovingNodeVisitor(),
        ];

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
        return [
            new TwigFilter('desc', [$this, 'desc']),
            new TwigFilter('meaning', [$this, 'meaning']),
        ];
    }

    /**
     * @param string $message
     * @param string $defaultMessage
     * @param int $count
     * @param array $arguments
     * @param string|null $domain
     * @param string|null $locale
     *
     * @return string
     */
    public function transchoiceWithDefault($message, $defaultMessage, $count, array $arguments = [], $domain = null, $locale = null)
    {
        if (null === $domain) {
            $domain = 'messages';
        }

        if (false === $this->translator->getCatalogue($locale)->defines($message, $domain)) {
            return $this->doTransChoice($defaultMessage, $count, array_merge(['%count%' => $count], $arguments), $domain, $locale);
        }

        return $this->doTransChoice($message, $count, array_merge(['%count%' => $count], $arguments), $domain, $locale);
    }

    private function doTransChoice($message, $count, array $arguments, $domain, $locale)
    {
        if ($this->translator instanceof LegacyTranslatorInterface) {
            return $this->translator->transChoice($message, $count, array_merge(['%count%' => $count], $arguments), $domain, $locale);
        }

        return $this->translator->trans($message, array_merge(['%count%' => $count], $arguments), $domain, $locale);
    }

    /**
     * @param mixed $v
     *
     * @return mixed
     */
    public function desc($v)
    {
        return $v;
    }

    /**
     * @param mixed $v
     *
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
}
