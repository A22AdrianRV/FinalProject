<?php
namespace App\Controller;
use App\Entity\Pokedex;
use App\Repository\PokedexRepository;
use App\Twig\getPokemon;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

    class PokedexController extends AbstractController{


        #[Route('/pokemon/add')]
        public function InsertPokemon(HttpClientInterface $http,EntityManagerInterface $entityManagerInterface):Response{
            

            set_time_limit(6000);
            $response = $http->request('GET',"https://pokeapi.co/api/v2/pokemon?limit=874&offset=151",[
                'headers' => [
                    'Accept' => 'applications/json',
                ],
            ]);

            $content = json_decode($response->getContent());
            foreach($content->results as $pokemon){
                $resp = $http->request('GET',$pokemon->url);
                $pokemonInfo = json_decode($resp->getContent());
                $poke = new Pokedex();
                $poke->setName($pokemonInfo->name);
                $poke->setTypes([$pokemonInfo->types[0]->type->name,count($pokemonInfo->types) > 1 ? $pokemonInfo->types[1]->type->name : null]);
                $AbilityArr = [];
                foreach($pokemonInfo->abilities as $ability){
                    $AbilityJSON = [
                        "name" => $ability->ability->name,
                        "is_hidden" => $ability->is_hidden,
                    ];
                    array_push($AbilityArr,$AbilityJSON);
                }
                $poke->setAbilities($AbilityArr);
                $arr = [];
                foreach($pokemonInfo->moves as $movement){
                    $details = $http->request('GET',$movement->move->url);
                    $moveInfo = json_decode($details->getContent());
                    $moveJSON = [
                        $movement->move->name => [
                            "learn_method" => $movement->version_group_details[0]->move_learn_method->name,
                            "level_learned_at" => $movement->version_group_details[0]->level_learned_at,
                            "type" => $moveInfo->damage_class->name,
                            "power" => $moveInfo->power
                        ]];

                    array_push($arr,$moveJSON);
                    
                }
                $poke->setMoves($arr);
                $poke->setStats([
                    "HP" => $pokemonInfo->stats[0]->base_stat,
                    "Attack" =>$pokemonInfo->stats[1]->base_stat,
                    "Defense"=>$pokemonInfo->stats[2]->base_stat,
                    "Special Attack"=>$pokemonInfo->stats[3]->base_stat,
                    "Special Defense"=>$pokemonInfo->stats[4]->base_stat,
                    "Speed"=>$pokemonInfo->stats[5]->base_stat,
                    "BST" => $pokemonInfo->stats[0]->base_stat + $pokemonInfo->stats[1]->base_stat + $pokemonInfo->stats[2]->base_stat + $pokemonInfo->stats[3]->base_stat +
                $pokemonInfo->stats[4]->base_stat + $pokemonInfo->stats[5]->base_stat,
                ]);
                $species = $http->request('GET',$pokemonInfo->species->url);
                $GetEvoChain = $http->request('GET',json_decode($species->getContent())->evolution_chain->url);
                $evoChain = json_decode($GetEvoChain->getContent());
                $poke->setEvolutionChain(json_decode(json_encode($evoChain->chain),true));
                $entityManagerInterface->persist($poke);
                $entityManagerInterface->flush();
                
        }
        return new Response(sprintf("Todos los pokes añadidos"));

    }

    #[Route('/')]
    public function pokedex(PokedexRepository $pokedexRepository,Request $request,$slug = null):Response{

        $pokemon = [];
        for($x = 1;$x<=20;$x++){
            $poke = $pokedexRepository->findById(rand(1,1025));
            array_push($pokemon,$poke);
        }
        $pokedex = new Pokedex();
        $form = $this->createFormBuilder($pokedex)
        ->add("name")
        ->add("save",SubmitType::class,["label"=>"Search"])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $pokemon = [];
            array_push($pokemon,$pokedexRepository->getPokemon($form->getData()->getName()));
        }
        return $this->render('pokemon/pokedex.html.twig',[
            "pokemons" => $pokemon,
            "url" => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/",
            "form" => $form,
        ]);
    }

    #[Route('/{slug}')]
    public function getInfo(PokedexRepository $pokedexRepository,Request $request,$slug):Response{
        $pokemon = $pokedexRepository->findById($slug);

        return $this->render('pokemon/getInfo.html.twig',[
            "pokemon" => $pokemon,
        ]);
    }
    

    #[Route('/teams')]
    public function teams():Response{

        return $this->render('pokemon/teams.html.twig',[]);
    }


}


?>