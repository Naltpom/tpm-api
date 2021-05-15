<?php

declare(strict_types=1);

namespace App\Security\Plugin;

use Http\Client\Common\Exception\ServerErrorException;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ErrorPlugin.
 */
class ErrorPlugin implements Plugin
{
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $promise = $next($request);

        return $promise->then(function (ResponseInterface $response) use ($request) {
            return $this->transformResponseToException($request, $response);
        });
    }

    private function transformResponseToException(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            throw new HttpException($response->getStatusCode(), $response->getReasonPhrase());
        }

        if ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            throw new ServerErrorException($response->getReasonPhrase(), $request, $response);
        }

        return $response;
    }
}
