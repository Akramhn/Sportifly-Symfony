<?php

namespace App\Entity;

use App\Repository\ActualiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use http\Message;
use Symfony\Component\Validator\Constraints as Asserts;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ActualiteRepository::class)]

class Actualite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Asserts\Length (min:3,minMessage:"le titre n'est pas assé long")]
    #[groups("actualite")]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[groups("actualite")]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Asserts\NotBlank(message:"veillez entrer un contenu")]
    #[groups("actualite")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Asserts\NotBlank(message:"veillez entrer un contenu")]
    #[groups("actualite")]
    private ?string $categorie = null;

    #[ORM\OneToMany(mappedBy: 'id_actualite', targetEntity: CommentaireAct::class)]
    private Collection $commentaireActs;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable]
    private ?\DateTimeInterface $date = null;



    public function __construct()
    {
        $this->commentaireActs = new ArrayCollection();
        $this->id_user = new ArrayCollection();
    }

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }


    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
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

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, CommentaireAct>
     */
    public function getCommentaireActs(): Collection
    {
        return $this->commentaireActs;
    }

    public function addCommentaireAct(CommentaireAct $commentaireAct): self
    {
        if (!$this->commentaireActs->contains($commentaireAct)) {
            $this->commentaireActs->add($commentaireAct);
            $commentaireAct->setIdActualite($this);
        }

        return $this;
    }

    public function removeCommentaireAct(CommentaireAct $commentaireAct): self
    {
        if ($this->commentaireActs->removeElement($commentaireAct)) {
            // set the owning side to null (unless already changed)
            if ($commentaireAct->getIdActualite() === $this) {
                $commentaireAct->setIdActualite(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return(string)$this->getId();
    }



}
