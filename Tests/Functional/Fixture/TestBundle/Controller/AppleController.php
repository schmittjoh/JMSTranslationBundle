<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Functional\Fixture\TestBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

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
        return ['nbApples' => 5];
    }
}
