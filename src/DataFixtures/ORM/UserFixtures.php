<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures.
 */
class UserFixtures extends Fixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->encoder = $container->get('security.password_encoder');
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $item) {
            $user = new User();

            $user
                ->setEmail($item['email'])
                ->setToken($item['token'] ?? null)
                ->setRoles($item['roles'])
                ->setPassword($item['password']);
            if (!empty($item['status'])) {
                $user->setStatus($item['status']);
            }
            if (!empty($item['dateDeleted'])) {
                $user->setDateDeleted($item['dateDeleted']);
            }
            if (!empty($item['deletedBy'])) {
                $user->setDeletedBy($item['deletedBy']);
            }

            $this->encodePassword($user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [
                'email' => 'bertrand.chauveau@gmail.com',
                'roles' => ['ROLE_USER', 'ROLE_TEST'],
                'password' => '85YCdaftq53E8M3Jn8jZ',
            ]
        ];
    }

    private function encodePassword(User $user): void
    {
        if (!$user->getPassword()) {
            return;
        }

        $encoded = $this->encoder->encodePassword($user, $user->getPassword());
        $user
            ->setPassword($encoded);
    }
}
