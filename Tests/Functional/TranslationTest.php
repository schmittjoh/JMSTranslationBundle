<?php

namespace JMS\TranslationBundle\Tests\Functional;

class TranslationTest extends BaseTestCase
{
    public function testTranschoiceWhenTranslationNotYetExtracted()
    {
        $client = $this->createClient();
        $client->request('GET', '/apples/view');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), substr($response, 0, 2000));
        $this->assertEquals("text.apples_remaining_does_not_exist\n\nThere are 5 apples", $response->getContent());
    }
}