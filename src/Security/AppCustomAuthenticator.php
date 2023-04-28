<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Repository\UserRepository;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{

    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(UserRepository $userRepository,private UrlGeneratorInterface $urlGenerator)
    {
        $this->userRepository= $userRepository;
    }


    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $user = $this->userRepository->findOneBy(['email' => $email]);

        $request->getSession()->set(Security::LAST_USERNAME, $email);
        if ($user && $user->isIsBlocked()) {
            throw new CustomUserMessageAuthenticationException('Your account has been blocked. Please contact the administrator for assistance.');
        }
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        $user=$token->getUser();
        if(in_array('ROLE_ADMIN',$user->getRoles(),true)){
            return new RedirectResponse($this->urlGenerator->generate('app_user_index'));
        }
        // For example:
        if(in_array('Role_Coach',$user->getRoles(),true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_listoffrefront'));
        }
        if(in_array('Role_USER',$user->getRoles(),true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_Alloffre'));
        }
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);

    }
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }




}
