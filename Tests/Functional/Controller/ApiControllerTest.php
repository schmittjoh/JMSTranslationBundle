<?php

namespace JMS\TranslationBundle\Tests\Functional\Controller;

use JMS\TranslationBundle\Tests\Functional\BaseTestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ApiControllerTest extends BaseTestCase
{
    public function testUpdateAction()
    {
        // Add a file
        $file = __DIR__.'/../Fixture/TestBundle/Resources/translations/navigation.en.yml';
        file_put_contents($file, 'main.home: Home');

        // Start application
        $client = static::createClient();
        $client->request('POST', '/_trans/api/configs/app/domains/navigation/locales/en/messages?id=main.home', array('_method'=>'PUT', 'message'=>'Away'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Verify that the file has new content
        $array = Yaml::parse(file_get_contents($file));
        $this->assertTrue(isset($array['main']));
        $this->assertTrue(isset($array['main']['home']));
        $this->assertEquals('Away', $array['main']['home']);

        // Remove the file
        unlink($file);
    }
}
