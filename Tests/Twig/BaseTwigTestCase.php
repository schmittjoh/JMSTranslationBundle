<?php

namespace JMS\TranslationBundle\Tests\Twig;

use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Bridge\Twig\Extension\TranslationExtension as SymfonyTranslationExtension;
use JMS\TranslationBundle\Twig\TranslationExtension;

abstract class BaseTwigTestCase extends \PHPUnit_Framework_TestCase
{
    protected final function parse($file, $debug = false)
    {
        $content = file_get_contents(__DIR__.'/Fixture/'.$file);

        $env = new \Twig_Environment();
        $env->addExtension(new SymfonyTranslationExtension(new IdentityTranslator(new MessageSelector())));
        $env->addExtension(new TranslationExtension($debug));
        $env->setLoader(new \Twig_Loader_String());

        return $env->parse($env->tokenize($content));
    }
}