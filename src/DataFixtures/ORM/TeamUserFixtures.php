<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Team;
use App\Entity\TeamUser;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class TeamUserFixtures.
 */
class TeamUserFixtures extends Fixture implements DependentFixtureInterface
{
    const LABEL = 'team-user-%s-%s';

    
    public function getDependencies()
    {
        return [
            TeamFixtures::class,
            UserFixtures::class
        ];
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $item) {
            $team = $this->getReference(sprintf(TeamFixtures::LABEL, strtolower($item['team'])));
            $user = $this->getReference(sprintf(UserFixtures::LABEL, base64_encode($item['user'])));

            $teamUser = new TeamUser();

            $teamUser
                ->setUser($user)
                ->setTeam($team);
            $manager->persist($teamUser);

            $this->setReference(sprintf(self::LABEL, $item['team'], $item['user']), $teamUser);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [
                'user' => 'bertrand.chauveau@gmail.com',
                'team' => 'My-team',
            ],
            [
                'user' => 'nathan.provost@gmail.com',
                'team' => 'My-team',
            ],
        ];
    }
}
