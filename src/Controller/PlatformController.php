<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class PlatformController extends AbstractController
{
    public function __construct(
        protected RequestStack $requestStack,
        protected ManagerRegistry $doctrine,
        protected TranslatorInterface $translator,
        protected KernelInterface $kernel,
        protected MailerInterface $mailer,
        protected LoggerInterface $logger
    ) {
    }

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

    #[Route('/dashboard', name: 'homepage')]
    public function dashboard(): Response
    {
        return $this->render('platform/dashboard.html.twig', [
            'controller_name' => 'PlatformController',
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be executed!
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
