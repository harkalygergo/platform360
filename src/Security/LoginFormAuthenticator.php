<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Repository\Platform\UserRepository;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UserRepository $userRepository,
        private RouterInterface $router,
    ) {}

    public function authenticate(Request $request): Passport
    {
        $id = $request->request->get('id', '');
        $password = $request->request->get('password', '');

        $request->getSession()->set('_security.last_username', $id);

        return new Passport(
            new UserBadge($id, function ($userIdentifier) {
                return $this->userRepository->findOneByAnyIdentifier($userIdentifier);
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse($targetPath);
        }

        return new \Symfony\Component\HttpFoundation\RedirectResponse($this->router->generate('homepage'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate(self::LOGIN_ROUTE);
    }
}
