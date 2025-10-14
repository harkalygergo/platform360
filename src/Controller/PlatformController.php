<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PlatformController extends AbstractController
{
    #[Route('/', name: 'app_platform')]
    public function index(TranslatorInterface $translator): Response
    {
        return $this->render('platform/auth.html.twig', [
            'controller_name' => 'PlatformController',
            'ID' => $translator->trans('ID'),
            'Password' => $translator->trans('password'),
        ]);
    }
}
