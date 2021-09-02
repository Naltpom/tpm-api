<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiKeyAuthenticator extends AbstractGuardAuthenticator
{
    public function start(Request $request, AuthenticationException $authException = null) {
        return new JsonResponse(['error' => 'Auth header required', REsponse::HTTP_UNAUTHORIZED]);
    }

    public function supports(Request $request) {

    }

    public function getCredentials(Request $request) {

    }
    public function getUser($credentials, UserProviderInterface $userProvider) 
    {}
    public function checkCredentials($credentials, UserInterface $user) 
    {}
    public function supportsRememberMe()
    {}
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) 
    {}
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {}

}