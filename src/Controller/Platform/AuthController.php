<?php

namespace App\Controller\Platform;

use App\Controller\PlatformController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthController extends PlatformController
{
    #[Route('/', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastId = $authenticationUtils->getLastUsername();

        return $this->render('platform/auth.html.twig', [
            'last_id' => $lastId,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be executed!
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
