<?php

namespace App\Entity;

use App\Repository\DuckRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: DuckRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Duck implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $duckname = null;


    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Quack::class)]
    private Collection $quacks;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Avatar $avatar = null;

    public function __construct()
    {
        $this->quacks = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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
     * @see PasswordAuthenticatedUserInterface
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
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDuckname(): ?string
    {
        return $this->duckname;
    }

    public function setDuckname(string $duckname): self
    {
        $this->duckname = $duckname;

        return $this;
    }


    /**
     * @return Collection<int, Quack>
     */
    public function getQuacks(): Collection
    {
        return $this->quacks;
    }

    public function addQuack(Quack $quack): self
    {
        if (!$this->quacks->contains($quack)) {
            $this->quacks->add($quack);
            $quack->setAuthor($this);
        }

        return $this;
    }

    public function removeQuack(Quack $quack): self
    {
        if ($this->quacks->removeElement($quack)) {
            // set the owning side to null (unless already changed)
            if ($quack->getAuthor() === $this) {
                $quack->setAuthor(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    
}
