<?php

declare(strict_types=1);

namespace App\Entity\Behavior;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Trait TimestampableTrait.
 */
trait TimestampableTrait
{
    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(type="datetime")
     *
     * @Groups({"timestampable"})
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(type="datetime")
     *
     * @Groups({"timestampable"})
     */
    private $dateModified;

    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    public function getDateModified(): \DateTime
    {
        return $this->dateModified;
    }
}
