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

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File\Fixture;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This is a sample controller class.
 *
 * It is used in unit tests to extract translations, and their respective description,
 * and meaning if specified.
 *
 * @author johannes
 */
class Controller
{
    private $translator;
    private $session;

    public function __construct(TranslatorInterface $translator, Session $session)
    {
        $this->translator = $translator;
        $this->session = $session;
    }

    public function indexAction()
    {
        $this->session->setFlash('foo', $this->translator->trans(/** @Desc("Foo bar") */ 'text.foo_bar'));
    }

    public function welcomeAction()
    {
        $this->session->setFlash('bar',
            /** @Desc("Welcome %name%! Thanks for signing up.") */
            $this->translator->trans('text.sign_up_successful', array('name' => 'Johannes')));
    }

    public function foobarAction()
    {
        $this->session->setFlash('archive',
            /** @Desc("Archive Message") @Meaning("The verb (to archive), describes an action") */
            $this->translator->trans('button.archive'));
    }

    public function nonExtractableButIgnoredAction()
    {
        /** @Ignore */ $this->translator->trans($foo);
        /** Foobar */
        /** @Ignore */ $this->translator->trans('foo', array(), $baz);
    }

    public function irrelevantDocComment()
    {
        /** @Foo @Bar */ $this->translator->trans('text.irrelevant_doc_comment', array(), 'baz');
    }

    public function arrayAccess()
    {
        $arr['foo']->trans('text.array_method_call');
    }

    public function assignToVar()
    {
        /** @Desc("The var %foo% should be assigned.") */
        return $this->translator->trans('text.var.assign', array('%foo%' => 'fooVar'));
    }
}
