<?php

declare(strict_types=1);

namespace App\Entity\Behavior;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Trait SoftDeletableTrait.
 */
trait SoftDeletableTrait
{
    /**
     * @var \DateTime
     *
     * @ApiProperty(
     *     attributes={
     *          "swagger_context"={
     *             "type"="string"
     *         }
     *     }
     * )
     *
     * @Groups({"soft-deletable"})
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDeleted;

    /**
     * @var string
     *
     * @ApiProperty(
     *     attributes={
     *          "swagger_context"={
     *             "type"="string"
     *         }
     *     }
     * )
     *
     * @Groups({"soft-deletable"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deletedBy;

    public function getDateDeleted(): ?\DateTime
    {
        return $this->dateDeleted;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDateDeleted(\DateTime $dateDeleted): self
    {
        $this->dateDeleted = $dateDeleted;

        return $this;
    }

    public function getDeletedBy(): ?string
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(string $deletedBy): self
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }
}
