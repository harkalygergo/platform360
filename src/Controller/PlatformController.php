<?php

namespace App\Controller;

use App\Entity\Platform\BillingProfile;
use App\Entity\Platform\Instance;
use App\Entity\Platform\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationFormType;

final class PlatformController extends AbstractController
{
    public function __construct(
        protected RequestStack $requestStack,
        protected \Doctrine\Persistence\ManagerRegistry $doctrine,
        protected TranslatorInterface $translator,
        protected KernelInterface $kernel,
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

                $instance = new Instance();
                $instance->setName($billing->getName() . ' instance');
                $instance->setOwner($user);
                $instance->setStatus(true);
                $instance->setCreatedAt(new \DateTimeImmutable());
                $instance->addUser($user);
                $instance->addBillingProfile($billing);

                $this->doctrine->getManager()->persist($user);
                $this->doctrine->getManager()->persist($billing);
                $this->doctrine->getManager()->persist($instance);
                $this->doctrine->getManager()->flush();

                return $this->redirectToRoute('app_dashboard');
            }
        }

        return $this->render('platform/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
