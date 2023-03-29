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

use Symfony\Component\HttpKernel\Kernel;

class RemovingNodeVisitorTest extends BaseTwigTestCase
{
    public function testRemovalWithSimpleTemplate(): void
    {
        $isSF5 = version_compare(Kernel::VERSION, '5.0.0') >= 0;

        $templateSuffix = $isSF5 ? '_sf5' : '';

        $expected = $this->parse('simple_template_compiled' . $templateSuffix . '.html.twig');
        $actual   = $this->parse('simple_template' . $templateSuffix . '.html.twig');

        $this->assertEquals($expected, $actual);
    }
}
