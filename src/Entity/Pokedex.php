<?php

namespace App\Entity;

use App\Repository\PokedexRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokedexRepository::class)]
class Pokedex
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $name = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $types = [];

    #[ORM\Column]
    private array $abilities = [];

    #[ORM\Column]
    private array $moves = [];



    #[ORM\Column]
    private array $stats = [];

    #[ORM\Column]
    private array $evolution_chain = [];


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

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(array $types): static
    {
        $this->types = $types;

        return $this;
    }

    public function getAbilities(): array
    {
        return $this->abilities;
    }

    public function setAbilities(array $abilities): static
    {
        $this->abilities = $abilities;

        return $this;
    }

    public function getMoves(): array
    {
        return $this->moves;
    }

    public function setMoves(array $moves): static
    {
        $this->moves = $moves;

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

    public function getEvolutionChain(): array
    {
        return $this->evolution_chain;
    }

    public function setEvolutionChain(array $evolution_chain): static
    {
        $this->evolution_chain = $evolution_chain;

        return $this;
    }

}
