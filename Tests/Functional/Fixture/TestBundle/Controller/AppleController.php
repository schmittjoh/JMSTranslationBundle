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

    /**
     * @Route("/view_sf5")
     * @Template("@Test/Apple/view_sf5.html.twig")
     */
    public function viewsf5Action()
    {
        return ['nbApples' => 5];
    }
}
