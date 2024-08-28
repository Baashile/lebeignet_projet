<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column]
    private ?float $montantTotal = null;

    #[ORM\Column(length: 255)]
    private ?string $statutPaiement = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $methodeLivraison = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateCommande = null;

    #[ORM\ManyToOne(targetEntity:User::class)]
    #[Assert\NotBlank]
    private ?User $utilisateur = null;

    #[ORM\ManyToOne(targetEntity:Client::class)]
    #[Assert\NotBlank]
    private ?Client $client = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: ElementCommande::class, cascade: ['persist', 'remove'])]
    #[Assert\NotBlank]
    private Collection $elementDeCommande;
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private $archived = false;

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function __construct()
    {
        $this->elementDeCommande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(float $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }

    public function getStatutPaiement(): ?string
    {
        return $this->statutPaiement;
    }

    public function setStatutPaiement(string $statutPaiement): static
    {
        $this->statutPaiement = $statutPaiement;

        return $this;
    }

    public function getMethodeLivraison(): ?string
    {
        return $this->methodeLivraison;
    }

    public function setMethodeLivraison(string $methodeLivraison): static
    {
        $this->methodeLivraison = $methodeLivraison;

        return $this;
    }

    public function getDateCommande(): ?\DateTimeImmutable
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeImmutable $dateCommade): static
    {
        $this->dateCommande = $dateCommade;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getElementDeCommande(): Collection
    {
        return $this->elementDeCommande;
    }

    public function addElementDeCommande(ElementCommande $elementDeCommande): static
    {
        if (!$this->elementDeCommande->contains($elementDeCommande)) {
            $this->elementDeCommande->add($elementDeCommande);
            $elementDeCommande->setCommande($this);
        }

        return $this;
    }

    public function removeElementDeCommande(ElementCommande $elementDeCommande): static
    {
        if ($this->elementDeCommande->removeElement($elementDeCommande)) {
            if ($elementDeCommande->getCommande() === $this) {
                $elementDeCommande->setCommande(null);
            }
        }

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

}
