<?php

declare(strict_types=1);

namespace App\Security;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

/**
 * Class ApplicationCodePlugin.
 */
class ApplicationCodePlugin implements Plugin
{
    /**
     * @var string
     */
    private $applicationCode;

    public function __construct(string $applicationCode)
    {
        $this->applicationCode = $applicationCode;
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        return $next($request->withHeader('X-CODE-APPLICATION', $this->applicationCode));
    }
}
