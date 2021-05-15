<?php

declare(strict_types=1);

namespace App\Logger\Processor;

use Monolog\Processor\ProcessorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseProcessor.
 */
class ResponseProcessor implements ProcessorInterface
{
        /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * HttpSubscriber constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public function __invoke(array $records)
    {
        /** @var Response|null $response */
        $response = $records['context']['response'] ?? null;

        if (!$response instanceof Response) {
            return $records;
        }

        $records['context']['response'] = [
            'status_code' => $response->getStatusCode(),
            'version' => $response->getProtocolVersion(),
            'headers' => $this->getHeaders($response),
        ];
        if ($response->getStatusCode() >= 400) {
            $records['context']['response']['body'] = (string) $response->getContent();
        }
        $this->logger->info('Records request', ['request' => $response->getContent()]);
        
        return $records;
    }

    private function getHeaders(Response $response): array
    {
        $headers = [];
        foreach (['cache-control', 'date', 'x-version', 'content-type', 'expires'] as $header) {
            $headers[$header] = $response->headers->get($header);
        }

        return $headers;
    }
}
