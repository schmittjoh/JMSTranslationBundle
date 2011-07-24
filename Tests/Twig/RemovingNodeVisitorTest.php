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

namespace JMS\TranslationBundle\Tests\Twig;

use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Bridge\Twig\Extension\TranslationExtension as SymfonyTranslationExtension;
use JMS\TranslationBundle\Twig\TranslationExtension;
use JMS\TranslationBundle\Twig\RemovingNodeVisitor;

class RemovingNodeVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testRemovalWithSimpleTemplate()
    {
        $expected = $this->process('simple_template_compiled.html.twig');
        $actual = $this->process('simple_template.html.twig');

        $this->assertEquals($expected, $actual);
    }

    private function process($file)
    {
        $content = file_get_contents(__DIR__.'/Fixture/'.$file);

        $env = new \Twig_Environment();
        $env->addExtension(new SymfonyTranslationExtension(new IdentityTranslator(new MessageSelector())));
        $env->addExtension(new TranslationExtension());

        return $env->parse($env->tokenize($content));
    }
}