<?php

namespace App\Entity;

use App\Repository\WishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WishRepository::class)]
/*#[UniqueEntity('title', 'Un Wish porte déjà ce nom !')]*/
#[ORM\UniqueConstraint(columns: ['title'])]
#[UniqueEntity(fields: ['title'], message: 'This title is already used by another user')]
/* informe doctrine qu'elle possède des events à checker ( prePersist ....)  */
#[ORM\HasLifecycleCallbacks]
class Wish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide')]
    #[Assert\Length(min: 4, minMessage: 'Le titre doit contenir au moins {{ limit }} caractères')]
    private ?string $title = null;


    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;


    #[ORM\Column(nullable: true)]
    private ?bool $isPublished = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(nullable: true)]
    private ?string $poster = null;

    #[ORM\ManyToOne(inversedBy: 'wishes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    /**
     * @var Collection<int, genre>
     */
    #[ORM\ManyToMany(targetEntity: genre::class, inversedBy: 'wishes')]
    private Collection $genres;

    #[ORM\ManyToOne(inversedBy: 'mywishes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User2 $user = null;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function setPoster(string $poster): static
    {
        $this->poster = $poster;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(?bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    /* AVANT PRE PERSIST
    public function setDateCreated(\DateTimeInterface $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }
    */
    #[ORM\PrePersist()]
    public function setDateCreated(): static
    {
        $this->dateCreated = new \DateTime();

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(genre $genre): static
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(genre $genre): static
    {
        $this->genres->removeElement($genre);

        return $this;
    }

    public function getUser(): ?User2
    {
        return $this->user;
    }

    public function setUser(?User2 $user): static
    {
        $this->user = $user;

        return $this;
    }

}
