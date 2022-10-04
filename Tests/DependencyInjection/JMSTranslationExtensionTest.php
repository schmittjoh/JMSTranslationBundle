<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\DependencyInjection;

use JMS\TranslationBundle\DependencyInjection\JMSTranslationExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class JMSTranslationExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new JMSTranslationExtension(),
        ];
    }

    /**
     * @test
     */
    public function defaultParametersAfterLoading()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('jms_translation.source_language', 'en');
    }

    /**
     * @test
     */
    public function basicParametersAfterLoading()
    {
        $locales = ['en', 'fr', 'es'];
        $this->load(['source_language' => 'fr', 'locales' => $locales]);

        $this->assertContainerBuilderHasParameter('jms_translation.source_language', 'fr');
        $this->assertContainerBuilderHasParameter('jms_translation.locales', $locales);
    }
}
