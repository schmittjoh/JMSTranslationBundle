<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Functional;

use Symfony\Component\HttpKernel\Kernel;

class TranslationTest extends BaseTestCase
{
    public function testTranschoiceWhenTranslationNotYetExtracted()
    {
        $isSf5 = version_compare(Kernel::VERSION, '5.0.0') >= 0;

        $url    = $isSf5 ? '/apples/view_sf5' : '/apples/view';
        $client = $this->createClient();
        $client->request('GET', $url);
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $expected = $isSf5 ? "There are 5 apples\n" : "There are 5 apples\n\nThere are 5 apples\n";
        $this->assertEquals($expected, $response->getContent());
    }
}
