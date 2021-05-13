<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TeamFixtures.
 */
class TeamFixtures extends Fixture
{
    const LABEL = 'team-%s';

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $key => $item) {
            $team = new Team();

            $team
                ->setTitle($item['title'])
                ->setDescription($item['description']);
            $manager->persist($team);
            $this->setReference(sprintf(self::LABEL, strtolower($item['slug'])), $team);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [
                'title' => 'My team',
                'slug' => 'My-team',
                'description' => 'lorem impsum the is description of lorem year',
            ]
        ];
    }
}
