<?php

namespace App\Controller;

use App\OAuth\Github;
use App\Security\GithubAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/login/oauth/github", name="app_login_oauth_github")
     *
     * @param Request $request
     * @param Github  $client
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
}
