<?php

namespace JMS\TranslationBundle\Tests\Functional\Controller;

use JMS\TranslationBundle\Tests\Functional\BaseTestCase;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ApiControllerTest extends BaseTestCase
{
    public function testUpdateAction()
    {
        $isSf4 = version_compare(Kernel::VERSION,'4.0.0') >= 0;

        // Add a file
        $file = $isSf4 ? __DIR__.'/../Fixture/TestBundle/Resources/translations/navigation.en.yaml' : __DIR__.'/../Fixture/TestBundle/Resources/translations/navigation.en.yml';
        $written = file_put_contents($file, 'main.home: Home');
        $this->assertTrue($written !== false && $written > 0);

        // Start application
        $client = static::createClient();
        $client->request('POST', '/_trans/api/configs/app/domains/navigation/locales/en/messages?id=main.home', array('_method'=>'PUT', 'message'=>'Away'));
if($client->getResponse()->getStatusCode() !== 200) {
  file_put_contents('/tmp/test-update.html',$client->getResponse());
}
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Verify that the file has new content
        $array = Yaml::parse(file_get_contents($file));

        if ($isSf4) {
            $this->assertTrue(isset($array['main.home']),print_r($array,true));
            $this->assertEquals('Away', $array['main.home']);
        } else {
            $this->assertTrue(isset($array['main']));
            $this->assertTrue(isset($array['main']['home']));
            $this->assertEquals('Away', $array['main']['home']);
        }

        // Remove the file
        unlink($file);
    }
}
