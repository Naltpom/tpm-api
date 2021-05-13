<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class TeamUser.
 *
 * @ApiResource(
 *     denormalizationContext={"groups"={"teamUser-write"}},
 *     collectionOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"teamUser-base", "teamUser-list", "blameable", "timestampable"}},
 *      },
 *      "post"={
 *          "normalization_context"={"groups"={"teamUser-base", "teamUser-list", "teamUser-details", "blameable", "timestampable"}},
 *          "security"="is_granted('ROLE_ADMIN')"
 *      }
 *     },
 *     itemOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"teamUser-base", "teamUser-list", "teamUser-details", "blameable", "timestampable"}}
 *      },
 *      "put"={
 *          "normalization_context"={"groups"={"teamUser-base", "teamUser-list", "teamUser-details", "blameable", "timestampable"}},
 *          "security"="is_granted('ROLE_ADMIN')"
 *      },
 *      "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 * 
 * @ORM\Entity()
 */
class TeamUser
{
    use Behavior\BlameableTrait;
    use Behavior\TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"team-details"})
     * 
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="teamUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Groups({"user-details"})
     * 
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="teamUsers")
     */
    private $team;

    /**
     * @Groups({"team-details", "user-details"})
     * 
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
