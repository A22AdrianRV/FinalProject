<?php
namespace App\Twig;

use App\Repository\PokedexRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class getPokemon extends AbstractExtension{
    public function getFilters(): array
    {
        return [
            new TwigFilter('getId', [$this, 'getId']),
        ];
    }

    public function getFunctions():array{
        return [
            new TwigFunction('getUrl',[$this,'getUrl']),
        ];
    }

    public function getId(string $url):string{
        $id = explode("/",$url)[count(explode("/",$url))-2];
        return $id;
    }

    public function getURL(int $id,string $url):string{
        return $id <= 920 ? $url."other/showdown/" . $id . ".gif": $url.$id.".png";
    }
}