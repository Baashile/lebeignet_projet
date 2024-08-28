<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $quantiteStock = null;

    /**
     * @var Collection<int, ElementCommande>
     */
    #[ORM\OneToMany(targetEntity: ElementCommande::class, mappedBy: 'produit')]
    private Collection $elementCommandes;

    public function __construct()
    {
        $this->elementCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantiteStock(): ?int
    {
        return $this->quantiteStock;
    }

    public function setQuantiteStock(int $quantiteStock): static
    {
        $this->quantiteStock = $quantiteStock;

        return $this;
    }

    /**
     * @return Collection<int, ElementCommande>
     */
    public function getElementCommandes(): Collection
    {
        return $this->elementCommandes;
    }

    public function addElementCommande(ElementCommande $elementCommande): static
    {
        if (!$this->elementCommandes->contains($elementCommande)) {
            $this->elementCommandes->add($elementCommande);
            $elementCommande->setProduit($this);
        }

        return $this;
    }

    public function removeElementCommande(ElementCommande $elementCommande): static
    {
        if ($this->elementCommandes->removeElement($elementCommande)) {
            // set the owning side to null (unless already changed)
            if ($elementCommande->getProduit() === $this) {
                $elementCommande->setProduit(null);
            }
        }

        return $this;
    }
}
