<?php

namespace JMS\TranslationBundle\Tests\Functional;

use Symfony\Component\Console\Output\Output as AbstractOutput;

class Output extends AbstractOutput
{
    private $content = '';

    public function doWrite($content, $newline)
    {
        $this->content .= $content;

        if ($newline) {
            $this->content .= "\n";
        }
    }

    public function getContent()
    {
        return $this->content;
    }
}