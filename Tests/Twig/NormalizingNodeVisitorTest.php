<?php

namespace JMS\TranslationBundle\Tests\Twig;

class NormalizingNodeVisitorTest extends BaseTwigTest
{
    public function testBinaryConcatOfConstants()
    {
        $this->assertEquals(
            $this->parse('binary_concat_of_constants_compiled.html.twig'),
            $this->parse('binary_concat_of_constants.html.twig')
        );
    }
}