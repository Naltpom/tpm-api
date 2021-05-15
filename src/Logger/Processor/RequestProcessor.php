<?php

declare(strict_types=1);

namespace App\Logger\Processor;

use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestProcessor.
 */
class RequestProcessor implements ProcessorInterface
{
    public function __invoke(array $records): array
    {
        /** @var Request|null $request */
        $request = $records['context']['request'] ?? null;

        if (!$request instanceof Request) {
            return $records;
        }

        $records['context']['request'] = [
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'headers' => $request->headers->all(),
            'body' => (string) $request->getContent(),
        ];

        return $records;
    }
}
