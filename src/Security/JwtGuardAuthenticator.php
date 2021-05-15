<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class JwtGuardAuthenticator.
 */
class JwtGuardAuthenticator extends AbstractGuardAuthenticator
{
    public function supports(Request $request): bool
    {
        return $request->headers->has('Authorization') &&
            0 === strpos($request->headers->get('Authorization'), 'Bearer');
    }

    public function getCredentials(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
        $tokenParts = explode('.', $token);
        $payload = json_decode(base64_decode($tokenParts[1], true), true);
        $payload['token'] = $token;

        return $payload;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $userProvider
            ->setToken($credentials['token'])
            ->loadUserByUsername($credentials['slug'])
        ;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $responseBody = [
            'code' => JsonResponse::HTTP_UNAUTHORIZED,
            'message' => sprintf(
                'Authentication failed: %s.',
                rtrim($exception->getMessageKey(), '.')
            ),
        ];

        return new JsonResponse($responseBody, JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        throw new HttpException(401, 'JWT Token not found');
    }
}
