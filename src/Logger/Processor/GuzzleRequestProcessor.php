<?php

declare(strict_types=1);

namespace App\Logger\Processor;

use GuzzleHttp\Psr7\Request;
use Monolog\Processor\ProcessorInterface;

/**
 * Class GuzzleRequestProcessor.
 */
class GuzzleRequestProcessor implements ProcessorInterface
{
    public function __invoke(array $records)
    {
        /** @var Request|null $request */
        $request = $records['context']['request'] ?? null;

        if (!$request instanceof Request) {
            return $records;
        }

        $records['context']['request'] = [
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'headers' => $request->getHeaders(),
            'body' => (string) $request->getBody(),
        ];

        return $records;
    }
}
