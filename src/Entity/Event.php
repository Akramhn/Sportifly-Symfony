<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Serializer\Annotation\Groups;



#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /*** @Groups({"event"})
     */
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message:"Type is required")]
    /**
     * @Groups({"event"})
     */
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:12,minMessage:"La description de l'évènement doit comporter au moins {{ limit }} caractéres")]
    /**
     * @Groups({"event"})
     */
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\GreaterThan('today')]
    /**
     * @Groups({"event"})
     */
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:"Type is required")]
    /**
     * @Groups({"event"})
     */
    private ?string $img = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:"Type is required")]
    /**
     * @Groups({"event"})
     */
    private ?string $lieu = null;



    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Participant::class)]
    /**
     * @Groups({"event"})
     */
    private Collection $participants;

    #[ORM\Column]
    private ?float $pos1 = null;

    #[ORM\Column]
    private ?float $pos2 = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?User $id_user = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }



    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setEvent($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getEvent() === $this) {
                $participant->setEvent(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getTitre();
    }

    public function getPos1(): ?float
    {
        return $this->pos1;
    }

    public function setPos1(float $pos1): self
    {
        $this->pos1 = $pos1;

        return $this;
    }

    public function getPos2(): ?float
    {
        return $this->pos2;
    }

    public function setPos2(float $pos2): self
    {
        $this->pos2 = $pos2;

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
