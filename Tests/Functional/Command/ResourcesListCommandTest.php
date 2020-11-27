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

namespace JMS\TranslationBundle\Tests\Functional\Command;

use Symfony\Component\Console\Input\ArgvInput;

final class ResourcesListCommandTest extends BaseCommandTestCase
{
    public function testList(): void
    {
        $input = new ArgvInput([
            'app/console',
            'translation:list-resources',
        ]);

        $expectedOutput =
            'Directories list :' . "\n"
           . '    - %kernel.root_dir%/Fixture/TestBundle/Resources/translations' . "\n"
           . 'done!' . "\n";

        $this->getApp()->run($input, $output = new Output());
        $this->assertEquals($expectedOutput, $output->getContent());
    }

    public function testListFiles(): void
    {
        $input = new ArgvInput([
            'app/console',
            'translation:list-resources',
            '--files',
        ]);

        $expectedOutput =
            'Resources list :' . "\n"
            . '    - %kernel.project_dir%/Fixture/TestBundle/Resources/translations/messages.en.php' . "\n"
            . 'done!' . "\n";

        $this->getApp()->run($input, $output = new Output());
        $this->assertEquals($expectedOutput, $output->getContent());
    }
}
