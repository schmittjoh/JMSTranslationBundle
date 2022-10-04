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

namespace JMS\TranslationBundle\Tests\Util;

use JMS\TranslationBundle\Util\FileUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class FileUtilsTest extends TestCase
{
    /**
     * @var string
     */
    private $translationDirectory;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->fileSystem = new Filesystem();
    }

    public function testAnEmptyDirectoryReturnsNoFiles(): void
    {
        $files = FileUtils::findTranslationFiles($this->translationDirectory);

        $this->assertEquals([], $files);
    }

    public function testNestedDirectoriesAreIgnored(): void
    {
        $nestedDirectory = $this->translationDirectory . '/nested';
        mkdir($nestedDirectory);
        touch($nestedDirectory . '/messages.en.xliff');

        $files = FileUtils::findTranslationFiles($this->translationDirectory);

        $this->assertEquals([], $files);
    }

    public function testOnlyTranslationFilesArePickedUp(): void
    {
        $this->createTranslationFile('not_a_translation_file.yaml');
        $this->createTranslationFile('not_a_translation_file.xliff');
        $this->createTranslationFile('not_a_translation_file.en');
        $this->createTranslationFile('messages.nl.xliff');
        $this->createTranslationFile('some-other.en.yaml');

        $files = FileUtils::findTranslationFiles($this->translationDirectory);

        $this->assertCount(2, $files);
        $this->assertArrayHasKey('messages', $files);
        $this->assertArrayHasKey('some-other', $files);
    }

    public function testRegularTranslationFileNamesAreParsed(): void
    {
        $this->createTranslationFile('messages.nl.yaml');

        $files = FileUtils::findTranslationFiles($this->translationDirectory);

        $this->assertArrayHasKey('messages', $files);
        $this->assertArrayHasKey('nl', $files['messages']);
        $this->assertEquals('yaml', $files['messages']['nl'][0]);
        $this->assertInstanceOf(SplFileInfo::class, $files['messages']['nl'][1]);
        $this->assertFalse($files['messages']['nl'][2]);
    }

    public function testIntlIcuTranslationFileNamesAreParsed(): void
    {
        $this->createTranslationFile('messages+intl-icu.en_GB.xliff');

        $files = FileUtils::findTranslationFiles($this->translationDirectory);

        $this->assertArrayHasKey('messages', $files);
        $this->assertArrayHasKey('en_GB', $files['messages']);
        $this->assertEquals('xliff', $files['messages']['en_GB'][0]);
        $this->assertInstanceOf(SplFileInfo::class, $files['messages']['en_GB'][1]);
        $this->assertTrue($files['messages']['en_GB'][2]);
    }

    /**
     * Creates a temporary directory which we can use to run the file utils
     * tests. It gets cleaned up afterwards.
     */
    protected function setUp(): void
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            $this->markTestSkipped(sprintf(
                "Can't execute FileUtils tests because %s is not writable",
                $tempDir
            ));
        }

        $this->translationDirectory = $tempDir . '/' . uniqid('jms_test_');
        $directoryCreated = mkdir($this->translationDirectory);
        if (!$directoryCreated) {
            $this->markTestSkipped(sprintf(
                "Can't execute FileUtils tests because %s could not be created",
                $this->translationDirectory
            ));
        }
    }

    protected function tearDown(): void
    {
        $this->fileSystem->remove($this->translationDirectory);
    }

    private function createTranslationFile(string $filename): void
    {
        touch($this->translationDirectory . '/' . $filename);
    }
}
