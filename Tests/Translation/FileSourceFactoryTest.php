<?php

namespace JMS\TranslationBundle\Tests\Translation;

use JMS\TranslationBundle\Translation\FileSourceFactory;
use Nyholm\NSA;

class FileSourceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test many different path to make sure we find the relative one.
     *
     * @dataProvider pathProvider
     */
    public function testGetRelativePath($root, $file, $expected, $message = '')
    {
        $factory = new FileSourceFactory($root);
        $result = NSA::invokeMethod($factory, 'getRelativePath', $file);

        $this->assertEquals($expected, $result, $message);
    }

    public function pathProvider()
    {
        return array(
            array(
                '/user/foo/application/app',
                '/user/foo/application/src/bundle/controller/index.php',
                '/../src/bundle/controller/index.php',
            ),

            array(
                '/user/foo/application/app/foo/bar',
                '/user/foo/application/src/bundle/controller/index.php',
                '/../../../src/bundle/controller/index.php',
            ),

            array(
                '/user/foo/application/app',
                '/user/foo/application/app/../src/AppBundle/Controller/DefaultController.php',
                '/../src/AppBundle/Controller/DefaultController.php',
                'Test with "/../" in the file path',
            ),

            array(
                '/user/foo/application/app/foo/bar/baz/biz/foo',
                '/user/foo/application/src/bundle/controller/index.php',
                '/../../../../../../src/bundle/controller/index.php',
                'Test when the root path is longer that file path',
            ),
        );
    }
}
