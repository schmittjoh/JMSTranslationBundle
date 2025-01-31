<?php

declare(strict_types=1);

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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Annotation\Desc;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Annotation\Meaning;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\Extractor\File\DefaultPhpFileExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\FormExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\TranslationContainerExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\ValidationExtractor;
use JMS\TranslationBundle\Translation\Extractor\FileExtractor;
use JMS\TranslationBundle\Translation\FileSourceFactory;
use JMS\TranslationBundle\Twig\TranslationExtension;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Bridge\Twig\Extension\TranslationExtension as SymfonyTranslationExtension;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

class FileExtractorTest extends TestCase
{
    public function testExtractWithSimpleTestFixtures()
    {
        $expected          = [];
        $basePath          = __DIR__ . '/Fixture/SimpleTest/';
        $fileSourceFactory = new FileSourceFactory('faux');

        // Controller
        $message = new Message('controller.foo');
        $message->addSource($fileSourceFactory->create(new \SplFileInfo($basePath . 'Controller/DefaultController.php'), 29));
        $message->setDesc('Foo');
        $expected['controller.foo'] = $message;

        // Form Model
        $expected['form.foo'] = new Message('form.foo');
        $expected['form.bar'] = new Message('form.bar');

        // Templates
        foreach (['php', 'twig'] as $engine) {
            $message = new Message($engine . '.foo');
            $message->addSource($fileSourceFactory->create(new \SplFileInfo($basePath . 'Resources/views/' . $engine . '_template.html.' . $engine), 1));
            $expected[$engine . '.foo'] = $message;

            $message = new Message($engine . '.bar');
            $message->setDesc('Bar');
            $message->addSource($fileSourceFactory->create(new \SplFileInfo($basePath . 'Resources/views/' . $engine . '_template.html.' . $engine), 3));
            $expected[$engine . '.bar'] = $message;

            $message = new Message($engine . '.baz');
            $message->setMeaning('Baz');
            $message->addSource($fileSourceFactory->create(new \SplFileInfo($basePath . 'Resources/views/' . $engine . '_template.html.' . $engine), 5));
            $expected[$engine . '.baz'] = $message;

            $message = new Message($engine . '.foo_bar');
            $message->setDesc('Foo');
            $message->setMeaning('Bar');
            $message->addSource($fileSourceFactory->create(new \SplFileInfo($basePath . 'Resources/views/' . $engine . '_template.html.' . $engine), 7));
            $expected[$engine . '.foo_bar'] = $message;
        }

        // File with global namespace.
        $message = new Message('globalnamespace.foo');
        $message->addSource($fileSourceFactory->create(new \SplFileInfo($basePath . 'GlobalNamespace.php'), 29));
        $message->setDesc('Bar');
        $expected['globalnamespace.foo'] = $message;

        $actual = $this->extract(__DIR__ . '/Fixture/SimpleTest')->getDomain('messages')->all();

        asort($expected);
        asort($actual);

        $this->assertEquals($expected, $actual);
    }

    private function extract($directory)
    {
        $twig = new Environment(new ArrayLoader([]));
        $twig->addExtension(new SymfonyTranslationExtension($translator = new IdentityTranslator()));
        $twig->addExtension(new TranslationExtension($translator));
        $loader = new FilesystemLoader(realpath(__DIR__ . '/Fixture/SimpleTest/Resources/views/'));
        $twig->setLoader($loader);

        $docParser = new DocParser();
        $docParser->setImports([
            'desc' => Desc::class,
            'meaning' => Meaning::class,
            'ignore' => Ignore::class,
        ]);
        $docParser->setIgnoreNotImportedAnnotations(true);

        $metadataFactoryClass = LazyLoadingMetadataFactory::class;

        $factory = new $metadataFactoryClass(class_exists(AnnotationLoader::class) ? new AnnotationLoader(new AnnotationReader()) : new AttributeLoader());

        $dummyFileSourceFactory = new FileSourceFactory('faux');

        $extractor = new FileExtractor($twig, new NullLogger(), [
            new DefaultPhpFileExtractor($docParser, $dummyFileSourceFactory),
            new TranslationContainerExtractor(),
            new TwigFileExtractor($twig, $dummyFileSourceFactory),
            new ValidationExtractor($factory),
            new FormExtractor($docParser, $dummyFileSourceFactory),
        ]);
        $extractor->setDirectory($directory);

        return $extractor->extract();
    }
}
