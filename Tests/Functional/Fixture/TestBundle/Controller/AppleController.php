<?php

declare(strict_types=1);

namespace JMS\TranslationBundle\Tests\Functional\Fixture\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Johannes
 */
#[AsController()]
#[Route('/apples')]
class AppleController extends AbstractController
{
    #[Route('/view')]
    public function viewAction(): Response
    {
        return $this->render('@Test/Apple/view.html.twig', ['nbApples' => 5]);
    }
}
