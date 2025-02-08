<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Functional;

class TranslationTest extends BaseTestCase
{
    public function testTranschoiceWhenTranslationNotYetExtracted(): void
    {
        $client = $this->createClient();
        $client->request('GET', '/apples/view');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $this->assertEquals("There are 5 apples\n", $response->getContent());
    }
}
