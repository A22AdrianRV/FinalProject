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

    /*
    *   Receives a url and extracts the digit of the pokemon from the end of it
    *
    */
    public function getId(string $url):string{
        $id = explode("/",$url)[count(explode("/",$url))-2];
        return $id;
    }

    /*
    *
    * Receives an id and a url and returns a different image based on the id,
    * this is because from the 920 id onwards there are no gifs so they wouldnt show on the page
    *
    */ 

    public function getURL(int $id,string $url):string{
        return $id <= 920 ? $url."other/showdown/" . $id . ".gif": $url.$id.".png";
    }
}