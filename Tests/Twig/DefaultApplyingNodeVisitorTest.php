<?php

namespace JMS\TranslationBundle\Tests\Twig;

class DefaultApplyingNodeVisitorTest extends BaseTwigTest
{
    public function testApply()
    {
        $this->assertEquals(
            $this->parse('apply_default_value_compiled.html.twig', true),
            $this->parse('apply_default_value.html.twig', true)
        );
    }
}