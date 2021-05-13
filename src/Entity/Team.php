<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Team.
 *
 * @ApiResource(
 *     denormalizationContext={"groups"={"team-write"}},
 *     collectionOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"team-base", "team-list", "blameable", "timestampable"}},
 *      },
 *      "post"={
 *          "normalization_context"={"groups"={"team-base", "team-list", "team-details", "blameable", "timestampable"}},
 *          "security"="is_granted('ROLE_ADMIN')"
 *      }
 *     },
 *     itemOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"team-base", "team-list", "team-details", "blameable", "timestampable"}}
 *      },
 *      "put"={
 *          "normalization_context"={"groups"={"team-base", "team-list", "team-details", "blameable", "timestampable"}},
 *          "security"="is_granted('ROLE_ADMIN')"
 *      },
 *      "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 *
 * @ORM\Entity()
 */
class Team
{
    use Behavior\BlameableTrait;
    use Behavior\TimestampableTrait;
    use Behavior\SoftDeletableTrait;

    /**
     * @ApiProperty(identifier=false)
     * 
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"team-write", "team-list", "team-details", "user-details"})
     * 
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * @Groups({"team-write", "team-list", "team-details", "user-details"})
     * 
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Groups({"team-write", "team-details"})
     * 
     * @ORM\OneToMany(targetEntity=TeamUser::class, mappedBy="team")
     */
    private $teamUsers;

    /**
     * @var string
     *
     * @ApiProperty(identifier=true)
     *
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     *
     * @Groups({"team-list", "team-details", "user-details"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    public function __construct()
    {
        $this->teamUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|TeamUser[]
     */
    public function getTeamUsers(): Collection
    {
        return $this->teamUsers;
    }

    public function addTeamUser(TeamUser $teamUser): self
    {
        if (!$this->teamUsers->contains($teamUser)) {
            $this->teamUsers[] = $teamUser;
            $teamUser->setTeam($this);
        }

        return $this;
    }

    public function removeTeamUser(TeamUser $teamUser): self
    {
        if ($this->teamUsers->removeElement($teamUser)) {
            // set the owning side to null (unless already changed)
            if ($teamUser->getTeam() === $this) {
                $teamUser->setTeam(null);
            }
        }

        return $this;
    }
}
