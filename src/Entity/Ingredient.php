<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le nom de l\'ingredient est obligatoire')]
    #[Assert\Length(min: 2, max: 50)]
    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[Assert\Positive(message: 'Le prix doit être strictement supérieur à 0.')]
    #[Assert\Range(min: 2, max: 200)]
    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $creatdAt = null;

    /**
     * @var Collection<int, Recette>
     */
    #[ORM\ManyToMany(targetEntity: Recette::class, mappedBy: 'ingredients')]
    private Collection $recettes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatdAt(): ?\DateTimeImmutable
    {
        return $this->creatdAt;
    }

    public function setCreatdAt(\DateTimeImmutable $creatdAt): static
    {
        $this->creatdAt = $creatdAt;

        return $this;
    }
    //ce constructeur permet d'assigner une date de creation a creatAd a chaque fois qu'on cree un ingredient
    public function __construct() {
        $this->creatdAt = new \DateTimeImmutable();
        $this->recettes = new ArrayCollection();
    }

    /**
     * @return Collection<int, Recette>
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): static
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->addIngredient($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): static
    {
        if ($this->recettes->removeElement($recette)) {
            $recette->removeIngredient($this);
        }

        return $this;
    }
}
