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

    #[Route('/', name: 'app_platform')]
    public function index(TranslatorInterface $translator): Response
    {
        return $this->render('platform/auth.html.twig', [
            'controller_name' => 'PlatformController',
            'ID' => $translator->trans('ID'),
            'Password' => $translator->trans('password'),
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('platform/dashboard.html.twig', [
            'controller_name' => 'PlatformController',
        ]);
    }
}
