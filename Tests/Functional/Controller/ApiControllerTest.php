<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Functional\Controller;

use JMS\TranslationBundle\Tests\Functional\BaseTestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ApiControllerTest extends BaseTestCase
{
    public function testUpdateAction(): void
    {
        // Start application
        $client    = static::createClient();
        $outputDir = $client->getContainer()->getParameter('translation_output_dir');

        $isSf4 = version_compare(Kernel::VERSION, '4.0.0') >= 0;

        // Add a file
        $file    = $isSf4 ? $outputDir . '/navigation.en.yaml' : $outputDir . '/navigation.en.yml';
        $written = file_put_contents($file, 'main.home: Home');
        $this->assertTrue($written !== false && $written > 0);

        $client->request('POST', '/_trans/api/configs/app/domains/navigation/locales/en/messages?id=main.home', ['_method' => 'PUT', 'message' => 'Away']);

        $fileContent = is_file($file) ? file_get_contents($file) : '';
        unlink($file);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Verify that the file has new content
        $array = Yaml::parse($fileContent);

        if ($isSf4) {
            $this->assertTrue(isset($array['main.home']), print_r($array, true));
            $this->assertEquals('Away', $array['main.home']);
        } else {
            $this->assertTrue(isset($array['main']));
            $this->assertTrue(isset($array['main']['home']));
            $this->assertEquals('Away', $array['main']['home']);
        }
    }
}
