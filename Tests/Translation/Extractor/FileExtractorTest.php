<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\TranslationBundle\Tests\Translation\Extractor;

use Symfony\Component\HttpKernel\Log\NullLogger;
use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Translation\Extractor\File\FormExtractor;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use JMS\TranslationBundle\Translation\Extractor\File\ValidationExtractor;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\TranslationContainerExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\DefaultPhpFileExtractor;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Bridge\Twig\Extension\TranslationExtension as SymfonyTranslationExtension;
use JMS\TranslationBundle\Twig\TranslationExtension;
use JMS\TranslationBundle\Translation\Extractor\FileExtractor;

class FileExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractWithSimpleTestFixtures()
    {
        $expected = array();
        $basePath = __DIR__.'/Fixture/SimpleTest/';

        // Controller
        $message = new Message('controller.foo');
        $message->addSource(new FileSource($basePath.'Controller/DefaultController.php', 27));
        $message->setDesc('Foo');
        $expected['controller.foo'] = $message;

        // Form Model
        $expected['form.foo'] = new Message('form.foo');
        $expected['form.bar'] = new Message('form.bar');

        // Templates
        foreach (array('php', 'twig') as $engine) {
            $message = new Message($engine.'.foo');
            $message->addSource(new FileSource($basePath.'Resources/views/'.$engine.'_template.html.'.$engine, 1));
            $expected[$engine.'.foo'] = $message;

            $message = new Message($engine.'.bar');
            $message->setDesc('Bar');
            $message->addSource(new FileSource($basePath.'Resources/views/'.$engine.'_template.html.'.$engine, 3));
            $expected[$engine.'.bar'] = $message;

            $message = new Message($engine.'.baz');
            $message->setMeaning('Baz');
            $message->addSource(new FileSource($basePath.'Resources/views/'.$engine.'_template.html.'.$engine, 5));
            $expected[$engine.'.baz'] = $message;

            $message = new Message($engine.'.foo_bar');
            $message->setDesc('Foo');
            $message->setMeaning('Bar');
            $message->addSource(new FileSource($basePath.'Resources/views/'.$engine.'_template.html.'.$engine, 7));
            $expected[$engine.'.foo_bar'] = $message;
        }

        $actual = $this->extract(__DIR__.'/Fixture/SimpleTest/')->getDomain('messages')->all();

        asort($expected);
        asort($actual);

        $this->assertEquals($expected, $actual);
    }

    private function extract($directory)
    {
        $twig = new \Twig_Environment();
        $twig->addExtension(new SymfonyTranslationExtension($translator = new IdentityTranslator(new MessageSelector())));
        $twig->addExtension(new TranslationExtension($translator));
        $loader=new \Twig_Loader_Filesystem(realpath(__DIR__."/Fixture/SimpleTest/Resources/views/"));
        $twig->setLoader($loader);

        $docParser = new DocParser();
        $docParser->setImports(array(
                        'desc' => 'JMS\TranslationBundle\Annotation\Desc',
                        'meaning' => 'JMS\TranslationBundle\Annotation\Meaning',
                        'ignore' => 'JMS\TranslationBundle\Annotation\Ignore',
        ));
        $docParser->setIgnoreNotImportedAnnotations(true);

        $factory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $extractor = new FileExtractor($twig, new NullLogger(), array(
            new DefaultPhpFileExtractor($docParser),
            new TranslationContainerExtractor(),
            new TwigFileExtractor($twig),
            new ValidationExtractor($factory),
            new FormExtractor($docParser),
        ));
        $extractor->setDirectory($directory);

        return $extractor->extract();
    }
}