<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class HttpSubscriber.
 */
class HttpSubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 2050],
            KernelEvents::RESPONSE => ['onKernelResponse', -2050],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $this->logger->info('Processing request', ['request' => $event->getRequest()]);
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $this->logger->info('Sending response', ['response' => $event->getResponse()]);
    }
}
