<?php

namespace App\Entity;

use App\Repository\CategorieActiviteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: CategorieActiviteRepository::class)]
class CategorieActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Activite")]
    private ?int $id = null;
    #[Assert\NotBlank(message:"merci de remplir le champ")]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;
    #[Assert\NotBlank(message:"merci de remplir le champ")]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function __toString()
    {
       return $this->nom;
    }

}
