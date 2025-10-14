<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PlatformController extends AbstractController
{
    #[Route('/platform', name: 'app_platform')]
    public function index(): Response
    {
        return $this->render('platform/index.html.twig', [
            'controller_name' => 'PlatformController',
        ]);
    }
}
