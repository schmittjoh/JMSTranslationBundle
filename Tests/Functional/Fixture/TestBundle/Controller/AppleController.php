<?php

namespace JMS\TranslationBundle\Tests\Functional\Fixture\TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/apples")
 * @author Johannes
 */
class AppleController
{
    /**
     * @Route("/view")
     * @Template("@Test/Apple/view.html.twig")
     */
    public function viewAction()
    {
        return array('nbApples' => 5);
    }
}
