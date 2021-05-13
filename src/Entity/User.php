<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class User.
 *
 * @ApiResource(
 *     iri="https://schema.org/Person",
 *     denormalizationContext={"groups"={"user-write"}},
 *     collectionOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"user-list", "soft-deletable", "timestampable"}}
 *      },
 *      "post"={
 *          "normalization_context"={"groups"={"user-list", "user-details", "soft-deletable", "timestampable"}},
 *      }
 *     },
 *     itemOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"user-list", "user-details", "soft-deletable", "timestampable"}},
 *          "security"="is_granted('ROLE_ADMIN') or object == user"
 *      },
 *      "put"={
 *          "normalization_context"={"groups"={"user-list", "user-details", "soft-deletable", "timestampable"}},
 *          "security"="is_granted('ROLE_ADMIN')"
 *      },
 *      "delete"={
 *          "security"="is_granted('ROLE_ADMIN') or object == user"
 *      }
 *     }
 * )
 * 
 * @Gedmo\SoftDeleteable(fieldName="dateDeleted", hardDelete=false)
 * 
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * 
 * @ORM\Entity()
 */
class User implements UserInterface
{
    use Behavior\TimestampableTrait;
    use Behavior\SoftDeletableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ApiProperty(identifier=true)
     *
     * @Groups({"user-list"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @var array
     * 
     * @ORM\Column(type="json")
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var array
     * 
     * @ORM\OneToMany(targetEntity=TeamUser::class, mappedBy="user", orphanRemoval=true)
     */
    private $teamUsers;

    /**
     * @var array
     *
     * @ApiProperty(
     *     attributes={
     *          "swagger_context"={
     *             "type": "object"
     *         }
     *     }
     * )
     *
     * @Groups({"user-list"})
     *
     * @ORM\Column(type="json")
     */
    private $status;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;
    
    /**
     * @var \DateTime|null
     *
     * @ApiProperty(
     *     attributes={
     *          "swagger_context"={
     *             "type"="string",
     *             "format"="date-time"
     *         }
     *     }
     * )
     *
     * @Groups({"user-list"})
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;
    
    public function __construct()
    {
        $this->teamUsers = new ArrayCollection();
        $this->slug = Uuid::uuid4();
        $this->roles = [];
        $this->status = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $teamUser->setUser($this);
        }

        return $this;
    }

    public function removeTeamUser(TeamUser $teamUser): self
    {
        if ($this->teamUsers->removeElement($teamUser)) {
            // set the owning side to null (unless already changed)
            if ($teamUser->getUser() === $this) {
                $teamUser->setUser(null);
            }
        }

        return $this;
    }

    public function getStatus(): array
    {
        return $this->status;
    }

    public function setStatus(array $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function hasStatus(string $status): bool
    {
        return !empty($this->status[$status]);
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTime $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }
}
