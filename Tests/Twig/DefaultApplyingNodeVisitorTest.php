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

class DefaultApplyingNodeVisitorTest extends BaseTwigTestCase
{
    public function testApply(): void
    {
        $this->assertEquals(
            $this->parse('apply_default_value_compiled.html.twig', true),
            $this->parse('apply_default_value.html.twig', true)
        );
    }

    /**
     * The visitor rewrites the "desc" filter into a Twig AST, which must be built with
     * the current node classes so that compiling templates stays deprecation-free.
     */
    public function testApplyDoesNotTriggerDeprecations(): void
    {
        $deprecations = [];
        set_error_handler(
            static function (int $errno, string $message) use (&$deprecations): bool {
                $deprecations[] = $message;

                return true;
            },
            E_USER_DEPRECATED
        );

        try {
            $this->parse('apply_default_value.html.twig', true);
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $deprecations);
    }
}
