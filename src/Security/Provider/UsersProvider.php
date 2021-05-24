<?php

declare(strict_types=1);

namespace App\Security\Provider;

use App\Entity\User;
use App\Enum\StatusEnum;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider.
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(LoggerInterface $logger, UserRepository $repository)
    {
        $this->logger = $logger;
        $this->repository = $repository;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $this->logger->info('User provider', ['email' => $username]);
        /** @var User|null $user */
        $user = $this->repository->findOneByUsernameOrEmail($username);

        return $this->validUser($user, $username, 'Connexion');
    }

    public function refreshUser(UserInterface $user)
    {
        $this->logger->info('Refresh User', ['user' => $user]);

        /** @var User $user */
        $refreshedUser = $this->repository->findOneBy(['slug' => $user->getSlug()]);

        return $this->validUser($refreshedUser, $user->getEmail(), 'Refresh');
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }

    private function validUser(?User $user, string $email, string $action): User
    {
        if (null === $user) {
            $this->logger->warning(sprintf('%s with bad username or email', $action), ['email' => $email]);
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $email));
        }
        
        /** EXEMPLE FOR USE OF WORKFLOW */
        // if ($user->hasStatus(StatusEnum::WAITING_PASSWORD_CHANGE_STATUS)) {
        //     $this->logger->warning(sprintf('%s valid but user have invalid password', $action), ['user' => $user]);
        //     throw new UsernameNotFoundException(sprintf('User "%s" not found.', $email));
        // }

        if (null !== $user->getDateDeleted()) {
            $this->logger->warning(sprintf('%s valid but user have been deleted', $action), ['user' => $user]);
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $email));
        }

        return $user;
    }
}
