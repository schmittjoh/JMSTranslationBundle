<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\DependencyInjection;

use JMS\TranslationBundle\DependencyInjection\JMSTranslationExtension;
use JMS\TranslationBundle\JMSTranslationBundle;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class JMSTranslationExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->setParameter('kernel.bundles', ['JMSTranslationBundle' => JMSTranslationBundle::class]);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new JMSTranslationExtension(),
        ];
    }

    #[Test()]
    public function defaultParametersAfterLoading(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('jms_translation.source_language', 'en');
    }

    #[Test()]
    public function basicParametersAfterLoading(): void
    {
        $locales = ['en', 'fr', 'es'];
        $this->load(['source_language' => 'fr', 'locales' => $locales]);

        $this->assertContainerBuilderHasParameter('jms_translation.source_language', 'fr');
        $this->assertContainerBuilderHasParameter('jms_translation.locales', $locales);
    }
}
