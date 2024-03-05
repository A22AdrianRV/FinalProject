<?php
namespace App\Twig;

use App\Repository\PokedexRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class getPokemon extends AbstractExtension{
    public function getFilters(): array
    {
        return [
            new TwigFilter('getId', [$this, 'getId']),
        ];
    }

    public function getId(string $url):string{
        $id = explode("/",$url)[count(explode("/",$url))-2];
        return $id;
    }
}