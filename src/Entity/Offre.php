<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Offre")]
    private ?int $id = null;
    #[Assert\GreaterThan('today',message: ("date deja pass.."))]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("Offre")]
    private ?\DateTimeInterface $date = null;



    #[ORM\Column(length: 255)]
    #[Groups("Offre")]

    private ?string $affiche = null;

    #[Assert\NotBlank(message:"merci de remplir le champ")]
    #[Assert\NotEqualTo(
        value: 0,
        message : "Le prix d'un Offre ne doit pas etre égal a 0"
    )]

    #[ORM\Column]
    #[Groups("Offre")]

    private ?float $prix = null;

    #[Assert\NotBlank(message:"merci de remplir le champ")]
    #[Assert\Length(
        min: 10,
        max: 200,
        minMessage: 'votre titre ne contient pas {{ limit }} characters',
        maxMessage: 'votre titre a depassé {{ limit }} characters',
    )]
    #[ORM\Column(type: Types::TEXT)]
    #[Groups("Offre")]

    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'id_offre', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\ManyToOne(inversedBy: 'offre')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("Offre")]

    private ?User $id_user = null;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    #[Groups("Offre")]

    private ?CategorieActivite $id_category = null;



    #[ORM\Column]
    private ?int $nbplace = null;

    #[ORM\OneToMany(mappedBy: 'id_offre', targetEntity: Stars::class)]
    private Collection $stars;







    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->stars = new ArrayCollection();
        $this->offerRatings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAffiche(): ?string
    {
        return $this->affiche;
    }

    public function setAffiche(string $affiche): self
    {
        $this->affiche = $affiche;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setIdOffre($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getIdOffre() === $this) {
                $reservation->setIdOffre(null);
            }
        }

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getIdCategory(): ?CategorieActivite
    {
        return $this->id_category;
    }

    public function setIdCategory(?CategorieActivite $id_category): self
    {
        $this->id_category = $id_category;

        return $this;
    }
//
//    /**
//     * @return Collection<int, Stars>
//     */




    public function getNbplace(): ?int
    {
        return $this->nbplace;
    }

    public function setNbplace(int $nbplace): self
    {
        $this->nbplace = $nbplace;

        return $this;
    }

    /**
     * @return Collection<int, Stars>
     */
    public function getStars(): Collection
    {
        return $this->stars;
    }

    public function addStar(Stars $star): self
    {
        if (!$this->stars->contains($star)) {
            $this->stars->add($star);
            $star->setIdOffre($this);
        }

        return $this;
    }

    public function removeStar(Stars $star): self
    {
        if ($this->stars->removeElement($star)) {
            // set the owning side to null (unless already changed)
            if ($star->getIdOffre() === $this) {
                $star->setIdOffre(null);
            }
        }

        return $this;
    }








}
