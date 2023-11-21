<?php

namespace App\Entity;

use App\Repository\ReferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReferencesRepository::class)]
#[ORM\Table(name: '`references`')]
class References
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\OneToOne(mappedBy: 'reference', cascade: ['persist', 'remove'])]
    private ?Produits $produits = null;

    public function __toString(): string
    {
        return $this->code;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getProduits(): ?Produits
    {
        return $this->produits;
    }

    public function setProduits(Produits $produits): static
    {
        // set the owning side of the relation if necessary
        if ($produits->getReference() !== $this) {
            $produits->setReference($this);
        }

        $this->produits = $produits;

        return $this;
    }
}
