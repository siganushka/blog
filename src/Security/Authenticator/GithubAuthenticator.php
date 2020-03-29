<?php

namespace App\Security\Authenticator;

use App\Entity\User;
use App\OAuth\Github;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class GithubAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;

    const STATE = '_state';

    private $entityManager;
    private $httpUtils;
    private $github;

    public function __construct(EntityManagerInterface $entityManager, HttpUtils $httpUtils, Github $github)
    {
        $this->entityManager = $entityManager;
        $this->httpUtils = $httpUtils;
        $this->github = $github;
    }

    public function supports(Request $request)
    {
        return $this->httpUtils->checkRequestPath($request, 'app_login_oauth_github')
            && $request->query->has('code');
    }

    public function getCredentials(Request $request)
    {
        $state = $request->query->get('state');
        if ($state !== $request->getSession()->get(self::STATE)) {
            throw new CustomUserMessageAuthenticationException('Bad authentication state.');
        }

        try {
            return $this->github->getAccessToken($request->query->get('code'));
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException($th->getMessage());
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->github->getUser($credentials['access_token']);
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException($th->getMessage());
        }

        try {
            $entity = $userProvider->loadUserByUsername($data['login']);
        } catch (UsernameNotFoundException $e) {
            $entity = new User();
            $entity->setRoles(['ROLE_READER']);
            $entity->setUsername($data['login']);
            $entity->setNickname($data['name']);
            $entity->setUrl($data['html_url']);
            $entity->setAvatar($data['avatar_url']);
            $entity->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }

        return $entity;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw $exception;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if (!$targetPath) {
            $targetPath = $this->httpUtils->generateUri($request, 'app_index');
        }

        return $this->httpUtils->createRedirectResponse($request, $targetPath);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        if (null === $authException) {
            $authException = new AccessDeniedException();
        }

        throw $authException;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
