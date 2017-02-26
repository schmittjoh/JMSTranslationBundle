<?php

namespace JMS\TranslationBundle\Tests\DependencyInjection;

use JMS\TranslationBundle\DependencyInjection\JMSTranslationExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class JMSTranslationExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return array(
            new JMSTranslationExtension(),
        );
    }

    /**
     * @test
     */
    public function default_parameters_after_loading()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('jms_translation.source_language', 'en');
    }

    /**
     * @test
     */
    public function basic_parameters_after_loading()
    {
        $locales = array('en', 'fr', 'es');
        $this->load(array('source_language' => 'fr', 'locales' => $locales));

        $this->assertContainerBuilderHasParameter('jms_translation.source_language', 'fr');
        $this->assertContainerBuilderHasParameter('jms_translation.locales', $locales);
    }
}
