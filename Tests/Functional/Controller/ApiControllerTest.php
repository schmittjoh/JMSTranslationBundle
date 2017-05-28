<?php

namespace JMS\TranslationBundle\Tests\Functional\Controller;

use JMS\TranslationBundle\Tests\Functional\BaseTestCase;
use JMS\TranslationBundle\Translation\LoaderManager;
use Symfony\Component\Yaml\Yaml;
use JMS\TranslationBundle\Translation\Loader\XliffLoader;

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

    public function testAddNote()
    {
        // Configuration
        $domain = 'navigation';
        $locale = 'en';
        $id = 'foo';
        $api_format = sprintf ('/_trans/api/configs/app/domains/%s/locales/%s/messages?id=%s&type=note&index=%%s',
            $domain, $locale, $id);

        // Add a file
        $file = __DIR__.'/../Fixture/TestBundle/Resources/translations/navigation.en.xliff';
        file_put_contents($file, file_get_contents(__DIR__ . '/../../Translation/Fixture/xliff_updated.xml'));

        // Setup the environment
        $loaders = array ('xliff' => new XliffLoader());
        $loader = new LoaderManager($loaders);

        // The message should exist, and should have no notes
        $catalogue = $loader->loadFile($file, 'xliff', $locale, $domain);
        $message = $catalogue->get($id, $domain);
        $this->assertEquals('bar', $message->getLocaleString(), 'Unable to verify starting environment.');
        $this->assertEmpty($message->getNotes(), 'The xliff file must not already contain notes.');

        // Start application
        $client = static::createClient();

        // Try an invalid index.  It should respond that the index is invalid, with a HTTP 200 error code
        $client->request('POST', sprintf ($api_format, 'bogus'),
                array('_method'=>'PUT', 'message'=>'Note with invalid index.'));
        $response = $client->getResponse();
        $json_response = json_decode($response->getContent(), true);
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertEquals(
                array ('status' => 'INVALID_INDEX', 'message' => 'Invalid index specified.'),
                $json_response
            );

        // Check the handling of over and under value notes
        $tests = array (
            array ('index' => '0',     'message' => 'Zero Test'),     /* Should be overwritten by next call */
            array ('index' => '0',     'message' => 'Zero Overwrite'),/* Should be overwritten by next call */
            array ('index' => '1',     'message' => 'One Test'),      /* Should be left alone */
            array ('index' => '2',     'message' => 'Two Test'),      /* Should be deleted by later call */
            array ('index' => '1000',  'message' => 'Three Test'),    /* Should be rewritten to three */
            array ('index' => '2',     'message' => ''),              /* Should erase number 2 */
        );
        foreach ($tests as $test) {
            $client->request('POST', sprintf ($api_format, $test['index']),
                array('_method'=>'PUT', 'message'=>$test['message']));
            $response = $client->getResponse();
            $json_response = json_decode($response->getContent(), true);
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertEquals(
                array ('status' => 'SUCCESS', 'message' => $test['message'] == '' ? 'Note was deleted.' : 'Note was saved.'),
                $json_response
            );
        }

        // Verify that the file has new content
        $catalogue = $loader->loadFile($file, 'xliff', $locale, $domain);
        $message = $catalogue->get($id, $domain);
        $validNotes = array (
            array (
                'text' => 'Zero Overwrite',
            ),
            array (
                'text' => 'One Test',
            ),
            array (
                'text' => 'Three Test',
            ),
        );
        $this->assertEquals($validNotes, $message->getNotes(), 'The notes were not properly updated.');

        // Remove the file
        unlink($file);
    }
}
