<?php

declare(strict_types=1);

namespace App\Security;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class JwtPlugin.
 */
class JwtPlugin implements Plugin
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return $next($request);
        }

        $user = $token->getUser();
        $token = $user->getPassword();

        return $next($request->withHeader('Authorization', sprintf('Bearer %s', $token)));
    }
}
