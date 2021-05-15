<?php

declare(strict_types=1);

namespace App\Security\Provider;

use ElevenLabs\Api\Service\ApiService;
use ElevenLabs\Api\Service\Resource\ErrorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\User as sfUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class AuthJwtUserProvider.
 */
class AuthJwtUserProvider implements UserProviderInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ApiService
     */
    private $client;

    /**
     * @var string|null
     */
    private $token;

    public function __construct(LoggerInterface $logger, ApiService $client)
    {
        $this->logger = $logger;
        $this->client = $client;
        $this->token = null;
    }

    public function loadUserByUsername($username)
    {
        $this->logger->info('User provider', ['username' => $username]);

        $response = $this->client->call(
            'getCheckCredentialsItem',
            ['authorization' => sprintf('Bearer %s', $this->token)]
        );
        if ($response instanceof ErrorInterface) {
            return null;
        }

        $data = $response->getData();

        return new sfUser(
            $data['username'],
            $this->token,
            $data['roles'],
            true,
            true,
            true,
            true,
            array_merge($data, ['slug' => $username])
        );
    }

    public function refreshUser(UserInterface $user)
    {
        $this->logger->info('Refresh User', ['user' => $user]);

        return $user;
    }

    public function supportsClass($class)
    {
        return sfUser::class === $class;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
