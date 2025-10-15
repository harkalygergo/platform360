<?php

namespace App\Controller\Platform;

use App\Controller\PlatformController;
use App\Entity\Platform\BillingProfile;
use App\Entity\Platform\Instance;
use App\Entity\Platform\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationFormType;

class RegisterController extends PlatformController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $billing = new BillingProfile();

        $form = $this->createForm(RegistrationFormType::class, [
            'user' => $user,
            'billing' => $billing,
        ]);

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            //dump($request->request->all());
            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();
                $user = $data['user'];
                $billing = $data['billing'];
                $user->setStatus(true);

                // create instance based on billing profile
                $instance = new Instance();
                $instance->setName($billing->getName() . ' instance');
                $instance->setOwner($user);
                $instance->setType('platform');
                $instance->setStatus(true);
                $instance->setCreatedAt(new \DateTimeImmutable());
                $instance->addUser($user);
                $instance->addBillingProfile($billing);

                $this->doctrine->getManager()->persist($user);
                $this->doctrine->getManager()->persist($billing);
                $this->doctrine->getManager()->persist($instance);
                $this->doctrine->getManager()->flush();

                $this->sendEmail($user, $billing);

                return $this->redirectToRoute('app_dashboard');
            }
        }

        return $this->render('platform/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
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
