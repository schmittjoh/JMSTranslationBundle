<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Functional\Controller;

use JMS\TranslationBundle\Tests\Functional\BaseTestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ApiControllerTest extends BaseTestCase
{
    public function testUpdateAction(): void
    {
        // Start application
        $client = static::createClient();
        $container = $client->getContainer();
        $outputDir = $container->getParameter('translation_output_dir');

        // Add a file
        $file    = $outputDir . '/navigation.en.yaml';
        $written = file_put_contents($file, 'main.home: Home');
        $this->assertTrue($written !== false && $written > 0);

        $client->request('PUT', '/_trans/api/configs/app/domains/navigation/locales/en/messages?id=main.home', ['message' => 'Away']);

        $fileContent = is_file($file) ? file_get_contents($file) : '';
        unlink($file);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Verify that the file has new content
        $array = Yaml::parse($fileContent);

        $this->assertTrue(isset($array['main.home']), print_r($array, true));
        $this->assertEquals('Away', $array['main.home']);
    }

    public function testUpdateWithHttpOverrideAction(): void
    {
        // Start application
        $client    = static::createClient();
        $container = $client->getContainer();

        if (!$container->getParameter('kernel.http_method_override')) {
            $this->markTestSkipped('This test does not work with http method override disabled.');
        }

        $outputDir = $container->getParameter('translation_output_dir');

        // Add a file
        $file    = $outputDir . '/navigation.en.yaml';
        $written = file_put_contents($file, 'main.home: Home');
        $this->assertTrue($written !== false && $written > 0);

        $client->request('POST', '/_trans/api/configs/app/domains/navigation/locales/en/messages?id=main.home', ['_method' => 'PUT', 'message' => 'Back']);

        $fileContent = is_file($file) ? file_get_contents($file) : '';
        unlink($file);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Verify that the file has new content
        $array = Yaml::parse($fileContent);

        $this->assertTrue(isset($array['main.home']), print_r($array, true));
        $this->assertEquals('Back', $array['main.home']);
    }
}
