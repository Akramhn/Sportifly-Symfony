<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("users")]

    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'please enter valid Email ')]
    #[Assert\Email(message: 'please enter valid Email ')]
    /**
     * @Assert\Regex(
     *     pattern="/@/",
     *     message="L'adresse email doit contenir le caractère '@'."
     * )
     */
    #[Groups("users")]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups("users")]
    /**
     * @ORM\Column(type="json")
     */
    private array $roles =[];

    /**
     * @var string The hashed password
     */

    #[ORM\Column]
    #[Assert\NotCompromisedPassword]
    /**  #[Assert\NotBlank(message: 'Veuillez saisir votre mot de passe')]
     #[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$/', message: 'Le mot de passe doit contenir au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial')]**/
    private ?string $password = null;


    /**

     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 4,
     *      max = 8,
     *      minMessage = "Votre nom doit etre au moins {{ limit }} characters long",
     *      maxMessage = "Votre Numero ne peut pas dépasser {{ limit }} characters"
     * )
     */
    #[ORM\Column(length: 255)]
   // #[Assert\Regex(pattern: '/^(?=.[a-z])(?=.[A-Z]).+$/',message:"Nom doit etre des lettres")]
    #[Groups("users")]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("users")]
    private ?string $diplome = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("users")]
    private ?string $experience = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("users")]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reset_token = null;
    #[ORM\Column(type: 'string', length: 10,nullable: true, options: ['default' => 'Actif'])]
    #[Groups("users")]
    private ?String $status;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Reclamations::class)]
    private Collection $reclamations;

    #[ORM\Column(type: "boolean")]
    #[Groups("users")]
    private $isBlocked = false;
    #[ORM\Column(type: "boolean")]
    #[Groups("users")]
    private $isApproved = false;

    #[ORM\Column(type: 'string', length: 10,nullable: true, options: ['default' => 'Actif'])]
    #[Groups("users")]
    private ?string $etat = "Actif";

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDiplome(): ?string
    {
        return $this->diplome;
    }

    public function setDiplome(?string $diplome): self
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(?string $experience): self
    {
        $this->experience = $experience;

        return $this;
    }



    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    /**
     * @return Collection<int, Reclamations>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamations $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamations $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUser() === $this) {
                $reclamation->setUser(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        if (is_null($this->id)) {
            return 'NULL';
        }
        return (string) $this->id;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return String|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param String|null $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function isIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(?bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function isIsApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(?bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEtat(): ?string
    {
        return $this->etat;
    }

    /**
     * @param string|null $etat
     */
    public function setEtat(?string $etat): void
    {
        $this->etat = $etat;
    }










}
