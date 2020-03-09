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

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File;

use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor;
use JMS\TranslationBundle\Translation\FileSourceFactory;
use JMS\TranslationBundle\Twig\DefaultApplyingNodeVisitor;
use JMS\TranslationBundle\Twig\RemovingNodeVisitor;
use JMS\TranslationBundle\Twig\TranslationExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension as SymfonyTranslationExtension;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Translation\IdentityTranslator;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Source;

class TwigFileExtractorTest extends TestCase
{
    public function testExtractSimpleTemplateInSF5()
    {
        $isSF5 = version_compare(Kernel::VERSION, '5.0.0') >= 0;

        if (! $isSF5) {
            $this->markTestSkipped('Test only available with Symfony 5+');
        }

        $expected          = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo    = new \SplFileInfo(__DIR__ . '/Fixture/simple_template_sf5.html.twig');

        $message = new Message('text.foo');
        $message->setDesc('Foo Bar');
        $message->setMeaning('Some Meaning');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 1));
        $expected->add($message);

        $message = new Message('text.bar');
        $message->setDesc('Foo');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 3));
        $expected->add($message);

        $message = new Message('text.baz');
        $message->setMeaning('Bar');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 5));
        $expected->add($message);

        $message = new Message('text.foo_bar', 'foo');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 7));
        $expected->add($message);

        $message = new Message('text.name', 'app');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 9));
        $expected->add($message);

        $message = new Message('foo.bar');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 11));
        $expected->add($message);

        $message = new Message('foo.bar2');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 13));
        $expected->add($message);

        $message = new Message('foo.bar3', 'app');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 15));
        $expected->add($message);

        $message = new Message('foo.bar4', 'app');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 17));
        $expected->add($message);

        $message = new Message('text.default_domain');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 19));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('simple_template_sf5.html.twig'));
    }

    public function testExtractSimpleTemplate()
    {
        $isSF5 = version_compare(Kernel::VERSION, '5.0.0') >= 0;

        if ($isSF5) {
            $this->markTestSkipped('Test only available with Symfony < 5');
        }

        $expected          = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo    = new \SplFileInfo(__DIR__ . '/Fixture/simple_template.html.twig');

        $message = new Message('text.foo');
        $message->setDesc('Foo Bar');
        $message->setMeaning('Some Meaning');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 1));
        $expected->add($message);

        $message = new Message('text.bar');
        $message->setDesc('Foo');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 3));
        $expected->add($message);

        $message = new Message('text.baz');
        $message->setMeaning('Bar');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 5));
        $expected->add($message);

        $message = new Message('text.foo_bar', 'foo');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 7));
        $expected->add($message);

        $message = new Message('text.name', 'app');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 9));
        $expected->add($message);

        $message = new Message('text.apple_choice', 'app');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 11));
        $expected->add($message);

        $message = new Message('foo.bar');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 13));
        $expected->add($message);

        $message = new Message('foo.bar2');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 15));
        $expected->add($message);

        $message = new Message('foo.bar3', 'app');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 17));
        $expected->add($message);

        $message = new Message('foo.bar4', 'app');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 19));
        $expected->add($message);

        $message = new Message('text.default_domain');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 21));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('simple_template.html.twig'));
    }

    public function testExtractEdit()
    {
        $expected          = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo    = new \SplFileInfo(__DIR__ . '/Fixture/edit.html.twig');

        $message = new Message('header.edit_profile');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 10));
        $expected->add($message);

        $message = new Message('text.archive');
        $message->setDesc('Archive');
        $message->setMeaning('The verb');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 13));
        $expected->add($message);

        $message = new Message('button.edit_profile');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 16));
        $expected->add($message);

        $message = new Message('link.cancel_profile');
        $message->setDesc('Back to Profile');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 17));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('edit.html.twig'));
    }

    public function testEmbeddedTemplate()
    {
        $expected          = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo    = new \SplFileInfo(__DIR__ . '/Fixture/embedded_template.html.twig');

        $message = new Message('foo');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 3));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('embedded_template.html.twig'));
    }

    private function extract($file, ?TwigFileExtractor $extractor = null)
    {
        $fileRealPath = __DIR__ . '/Fixture/' . $file;
        if (! is_file($fileRealPath)) {
            throw new RuntimeException(sprintf('The file "%s" does not exist.', $fileRealPath));
        }

        $env = new Environment(new ArrayLoader([]));
        $env->addExtension(new SymfonyTranslationExtension($translator = new IdentityTranslator()));
        $env->addExtension(new TranslationExtension($translator, true));
        $env->addExtension(new RoutingExtension(new UrlGenerator(new RouteCollection(), new RequestContext())));
        $env->addExtension(new FormExtension());

        foreach ($env->getNodeVisitors() as $visitor) {
            if ($visitor instanceof DefaultApplyingNodeVisitor) {
                $visitor->setEnabled(false);
            }

            if (! ($visitor instanceof RemovingNodeVisitor)) {
                continue;
            }

            $visitor->setEnabled(false);
        }

        if ($extractor === null) {
            $extractor = new TwigFileExtractor($env, new FileSourceFactory('faux'));
        }

        $ast = $env->parse($env->tokenize(new Source(file_get_contents($fileRealPath), $fileRealPath)));

        $catalogue = new MessageCatalogue();
        $extractor->visitTwigFile(new \SplFileInfo($fileRealPath), $catalogue, $ast);

        return $catalogue;
    }

    protected function getFileSourceFactory()
    {
        return new FileSourceFactory('faux');
    }
}
