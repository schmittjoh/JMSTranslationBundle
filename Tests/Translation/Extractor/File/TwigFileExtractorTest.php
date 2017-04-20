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

namespace JMS\TranslationBundle\Tests\Translation\Extractor\File;

use JMS\TranslationBundle\Translation\FileSourceFactory;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use JMS\TranslationBundle\Twig\RemovingNodeVisitor;
use JMS\TranslationBundle\Twig2\RemovingNodeVisitor as Twig2RemovingNodeVisitor;
use JMS\TranslationBundle\Twig\DefaultApplyingNodeVisitor;
use JMS\TranslationBundle\Twig2\DefaultApplyingNodeVisitor as Twig2DefaultApplyingNodeVisitor;
use JMS\TranslationBundle\Exception\RuntimeException;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Bridge\Twig\Extension\TranslationExtension as SymfonyTranslationExtension;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Twig\TranslationExtension;
use JMS\TranslationBundle\Twig2\TranslationExtension as Twig2TranslationExtension;
use JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor;
use JMS\TranslationBundle\Translation\Extractor\File\Twig2FileExtractor;
use Symfony\Bridge\Twig\Extension\FormExtension;

class TwigFileExtractorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Twig_Environment */
    protected $env;

    /** @var \JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor */
    protected $extractor;

    public function setUp()
    {
        $this->env = new \Twig_Environment(new \Twig_Loader_Array(array()));
        $this->env->addExtension(new SymfonyTranslationExtension($translator = new IdentityTranslator(new MessageSelector())));

        if (defined('Twig_Environment::MAJOR_VERSION') && \Twig_Environment::MAJOR_VERSION > 1) {
            $this->env->addExtension(new Twig2TranslationExtension($translator, true));
        } else {
            $this->env->addExtension(new TranslationExtension($translator, true));
        }

        $this->env->addExtension(new RoutingExtension(new UrlGenerator(new RouteCollection(), new RequestContext())));
        $this->env->addExtension(new FormExtension(new TwigRenderer(new TwigRendererEngine())));

        foreach ($this->env->getNodeVisitors() as $visitor) {
            if ($visitor instanceof DefaultApplyingNodeVisitor || $visitor instanceof Twig2DefaultApplyingNodeVisitor) {
                $visitor->setEnabled(false);
            }
            if ($visitor instanceof RemovingNodeVisitor || $visitor instanceof Twig2RemovingNodeVisitor) {
                $visitor->setEnabled(false);
            }
        }

        if (defined('Twig_Environment::MAJOR_VERSION') && \Twig_Environment::MAJOR_VERSION > 1) {
            $this->extractor = new Twig2FileExtractor($this->env, new FileSourceFactory('faux'));
        } else {
            $this->extractor = new TwigFileExtractor($this->env, new FileSourceFactory('faux'));
        }
    }

    public function testExtractSimpleTemplate()
    {
        $expected = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/simple_template.html.twig');

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
        $expected = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/edit.html.twig');

        $message = new Message('header.edit_profile');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 10));
        $expected->add($message);

        $message = new Message("text.archive");
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
        $expected = new MessageCatalogue();
        $fileSourceFactory = $this->getFileSourceFactory();
        $fixtureSplInfo = new \SplFileInfo(__DIR__.'/Fixture/embedded_template.html.twig');

        $message = new Message('foo');
        $message->addSource($fileSourceFactory->create($fixtureSplInfo, 3));
        $expected->add($message);

        $this->assertEquals($expected, $this->extract('embedded_template.html.twig'));
    }

    private function extract($file, TwigFileExtractor $extractor = null)
    {
        if (!is_file($file = __DIR__.'/Fixture/'.$file)) {
            throw new RuntimeException(sprintf('The file "%s" does not exist.', $file));
        }

        if (null === $extractor) {
            $extractor = $this->extractor;
        }

        $ast = $this->env->parse($this->env->tokenize(new \Twig_Source(file_get_contents($file), $file)));

        $catalogue = new MessageCatalogue();
        $extractor->visitTwigFile(new \SplFileInfo($file), $catalogue, $ast);

        return $catalogue;
    }

    protected function getFileSourceFactory()
    {
        return new FileSourceFactory('faux');
    }
}
