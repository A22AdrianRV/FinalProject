namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class getPokemon extends AbstractExtension{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('Prueba', [$this, 'getId']),
        ];
    }

    public function getId(string $name):int{
        return 4;
    }
}