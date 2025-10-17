<?php

namespace App\Controller\Platform;

use App\Controller\PlatformController;
use App\Entity\Platform\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class DashboardController extends PlatformController
{
    #[Route('/dashboard', name: 'homepage')]
    public function dashboard(): Response
    {
        return $this->render('platform/dashboard/dashboard.html.twig', [
            'controller_name' => 'PlatformController',
        ]);
    }
}
