<?php

namespace JMS\TranslationBundle\Tests\Functional;

use Symfony\Component\HttpKernel\Kernel;

class TranslationTest extends BaseTestCase
{
    public function testTranschoiceWhenTranslationNotYetExtracted()
    {
        $isSf4 = version_compare(Kernel::VERSION,'4.0.0') >= 0;

        // Add a file
        $file = $isSf4 ? __DIR__.'/Fixture/TestBundle/Resources/translations/navigation.en.yaml' : __DIR__.'/Fixture/TestBundle/Resources/translations/navigation.en.yml';
        file_put_contents($file, '');

        $client = static::createClient();
        $client->request('GET', '/apples/view');
	    $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), substr($response, 0, 2000));
        $this->assertEquals("There are 5 apples\n\nThere are 5 apples", $response->getContent());

        // Remove the file
        unlink($file);
    }
}
