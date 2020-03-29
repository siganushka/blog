<?php

namespace App\Security\Http\Logout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class RefererLogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    use TargetPathTrait;

    private $httpUtils;

    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    public function onLogoutSuccess(Request $request)
    {
        $target = $request->headers->get('referer') ?? '/';

        return $this->httpUtils->createRedirectResponse($request, $target);
    }
}
