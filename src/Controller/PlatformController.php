<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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

    public function sendEmail($user, $billing)
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to($_ENV['ADMIN_EMAIL'])
            ->subject('new registration')
            ->text( '
                User: ' . $user->getId() . '
                Billing Profile: ' . $billing->getId()
            );

        $this->mailer->send($email);
    }
}
