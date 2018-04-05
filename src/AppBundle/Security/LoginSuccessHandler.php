<?php

namespace AppBundle\Security;

use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Redirector for admin login
 *
 * @Service(id="login_success_handler")
 * @Tag("monolog.logger")
 *
 */
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected
        $router,
        $checker;

    /**
     * @param Router $router
     * @param AuthorizationChecker $checker
     *
     * @InjectParams({
     *     "router" = @Inject("router"),
     *     "checker" = @Inject("security.authorization_checker")
     * })
     */
    public function __construct(Router $router, AuthorizationChecker $checker)
    {
        $this->router = $router;
        $this->checker = $checker;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $url = 'home';

        if($this->checker->isGranted('ROLE_ADMIN')) {
            $url = 'sonata_admin_redirect';
        }

        return new RedirectResponse($this->router->generate($url));
    }
}