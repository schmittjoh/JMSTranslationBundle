<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Translation;

use JMS\TranslationBundle\Translation\FileSourceFactory;
use Nyholm\NSA;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileSourceFactoryTest extends TestCase
{
    #[DataProvider('pathProvider')]
    public function testGetRelativePath($root, $projectRoot, $file, $expected, $message = ''): void
    {
        $factory = new FileSourceFactory($root, $projectRoot);
        $result  = NSA::invokeMethod($factory, 'getRelativePath', $file);

        $this->assertEquals($expected, $result, $message);
    }

    public static function pathProvider(): array
    {
        return [
            [
                '/user/foo/application/app',
                null,
                '/user/foo/application/src/bundle/controller/index.php',
                '/../src/bundle/controller/index.php',
            ],

            [
                '/user/foo/application/app/foo/bar',
                null,
                '/user/foo/application/src/bundle/controller/index.php',
                '/../../../src/bundle/controller/index.php',
            ],

            [
                '/user/foo/application/app',
                null,
                '/user/foo/application/app/../src/AppBundle/Controller/DefaultController.php',
                '/../src/AppBundle/Controller/DefaultController.php',
                'Test with "/../" in the file path',
            ],

            [
                '/user/foo/application/app/foo/bar/baz/biz/foo',
                null,
                '/user/foo/application/src/bundle/controller/index.php',
                '/../../../../../../src/bundle/controller/index.php',
                'Test when the root path is longer that file path',
            ],

            [
                '/user/foo/application/app',
                '/user/foo/application',
                '/user/foo/application/src/bundle/controller/index.php',
                '/src/bundle/controller/index.php',
            ],

            [
                '/user/foo/application/app/foo/bar',
                '/user/foo/application/src/foo/bar',
                '/user/foo/application/src/bundle/controller/index.php',
                '/../../bundle/controller/index.php',
            ],

            [
                '/user/foo/application/app',
                '/user/foo/application',
                '/user/foo/application/app/../src/AppBundle/Controller/DefaultController.php',
                '/app/../src/AppBundle/Controller/DefaultController.php',
                'Test with "/../" in the file path',
            ],

            [
                '/user/foo/application/app/foo/bar/baz/biz/foo',
                '/user/foo/application/src/foo/bar/baz/biz/foo',
                '/user/foo/application/src/bundle/controller/index.php',
                '/../../../../../bundle/controller/index.php',
                'Test when the root path is longer that file path',
            ],

        ];
    }
}
