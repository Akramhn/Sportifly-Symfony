<?php

namespace App\Entity;

use App\Repository\ActiviterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: ActiviterRepository::class)]
class Activiter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Activite")]
    private ?int $id = null;
    #[Assert\NotBlank(message:"merci de remplir le champ")]
    #[Assert\Length(
        min: 2,
        max: 20,
        minMessage: 'votre titre ne contient pas {{ limit }} characters',
        maxMessage: 'votre titre a depassé {{ limit }} characters',
    )]
    #[Groups("Activite")]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Assert\GreaterThan('today',message: ("date deja pass.."))]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("Activite")]
    private ?\DateTimeInterface $date_debut = null;
    #[Assert\Expression('this.getDateDebut()<this.getDateFin()',message: ("erreur periode"))]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("Activite")]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\ManyToOne(inversedBy: 'activiters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("Activite")]
    private ?CategorieActivite $ref_categ = null;

    #[ORM\ManyToOne(inversedBy: 'activiters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("Activite")]
    private ?User $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getRefCateg(): ?CategorieActivite
    {
        return $this->ref_categ;
    }

    public function setRefCateg(?CategorieActivite $ref_categ): self
    {
        $this->ref_categ = $ref_categ;

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
}
