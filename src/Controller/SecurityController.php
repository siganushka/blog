<?php

namespace App\Controller;

use App\OAuth\Github;
use App\Security\Authenticator\GithubAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @Route("/login", name="app_login", methods={"GET"})
     */
    public function login(Request $request)
    {
        if (null !== $referer = $request->headers->get('referer')) {
            $this->saveTargetPath($request->getSession(), 'main', $referer);
        }

        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/login/oauth/github", name="app_login_oauth_github", methods={"GET"})
     *
     * @return Response
     */
    public function loginWithGithub(Request $request, Github $client)
    {
        $state = bin2hex(random_bytes(8));

        $session = $request->getSession();
        $session->set(GithubAuthenticator::STATE, $state);

        $callback = $this->generateUrl('app_login_oauth_github', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $redirect = $client->getAuthorizeUrl($callback, $state);

        return $this->redirect($redirect);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
    }
}
