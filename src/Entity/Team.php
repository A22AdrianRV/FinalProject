<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $Moves = [];

    #[ORM\Column]
    private array $stats = [];

    #[ORM\Column(length: 50)]
    private ?string $ability = null;

    #[ORM\Column(length: 20)]
    private ?string $nature = null;

    #[ORM\ManyToOne(inversedBy: 'TeamName')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pokedex $PokemonName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoves(): array
    {
        return $this->Moves;
    }

    public function setMoves(array $Moves): static
    {
        $this->Moves = $Moves;

        return $this;
    }

    public function getStats(): array
    {
        return $this->stats;
    }

    public function setStats(array $stats): static
    {
        $this->stats = $stats;

        return $this;
    }

    public function getAbility(): ?string
    {
        return $this->ability;
    }

    public function setAbility(string $ability): static
    {
        $this->ability = $ability;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(string $nature): static
    {
        $this->nature = $nature;

        return $this;
    }

    public function getPokemonName(): ?Pokedex
    {
        return $this->PokemonName;
    }

    public function setPokemonName(?Pokedex $PokemonName): static
    {
        $this->PokemonName = $PokemonName;

        return $this;
    }
}
