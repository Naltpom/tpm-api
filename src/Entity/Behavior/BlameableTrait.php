<?php

declare(strict_types=1);

namespace App\Entity\Behavior;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Trait BlameableTrait.
 */
trait BlameableTrait
{
    /**
     * @var string
     *
     * @Gedmo\Blameable(on="create")
     *
     * @ORM\Column(type="string", length=255)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"blameable"})
     */
    private $createdBy;

    /**
     * @var string
     *
     * @Gedmo\Blameable(on="update")
     *
     * @ORM\Column(type="string", length=255)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"blameable"})
     */
    private $modifiedBy;

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getModifiedBy(): string
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(string $modifiedBy): self
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }
}
