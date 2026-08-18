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

namespace JMS\TranslationBundle\Tests\Twig;

use JMS\TranslationBundle\Twig\TranslationExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\TranslationExtension as SymfonyTranslationExtension;
use Symfony\Component\Translation\IdentityTranslator;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Node\Node;
use Twig\Source;

abstract class BaseTwigTestCase extends TestCase
{
    final protected function parse($file, $debug = false): string
    {
        $env = $this->createEnvironment($debug);

        return $env->compile($this->parseTemplate($env, $file));
    }

    /**
     * Returns the body of the parsed template, i.e. the AST as produced by the bundle's node visitors.
     */
    final protected function parseAst(string $file, bool $debug = false): Node
    {
        return $this->parseTemplate($this->createEnvironment($debug), $file);
    }

    private function createEnvironment(bool $debug): Environment
    {
        $env = new Environment(new ArrayLoader([]));
        $env->addExtension(new SymfonyTranslationExtension(new IdentityTranslator()));
        $env->addExtension(new TranslationExtension(null, $debug));

        return $env;
    }

    private function parseTemplate(Environment $env, string $file): Node
    {
        $content = file_get_contents(__DIR__ . '/Fixture/' . $file);

        return $env->parse($env->tokenize(new Source($content, 'whatever')))->getNode('body');
    }
}
