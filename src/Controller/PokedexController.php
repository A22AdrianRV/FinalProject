<?php
namespace App\Controller;
use App\Entity\Pokedex;
use App\Entity\Team;
use App\Repository\PokedexRepository;
use App\Repository\TeamRepository;
use App\Twig\getPokemon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

    class PokedexController extends AbstractController{


        // #[Route('/pokemon/add')]
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

    #[Route('/',"app_pokedex")]
    public function pokedex(PokedexRepository $pokedexRepository,Request $request):Response{

        $pokemon = [];
        for($x = 1;$x<=20;$x++){
            $poke = $pokedexRepository->findById(rand(1,1025));
            array_push($pokemon,$poke);
        }
        $pokedex = new Pokedex();
        $pokedex->setTypes(["Type1"=>"","Type2"=>""]);
        $form = $this->createFormBuilder($pokedex)
        ->add("name",TextType::class,[
            "required"=>false,
        ])
        ->add("getByName",SubmitType::class,["label"=>"Search"])
        ->add("types",CollectionType::class,[
            'entry_type'=>ChoiceType::class,
            'entry_options'  => [
                'choices'  => [
                    "Select" => null,
                    "Normal" => "normal",
                    "Fighting" => "fighting",
                    "Flying" => "flying",
                    "Poison" => "poison",
                    "Ground" => "ground",
                    "Rock" => "rock",
                    "Bug" => "bug",
                    "Ghost" => "ghost",
                    "Steel" => "steel",
                    "Fire" => "fire",
                    "Water" => "water",
                    "Grass" => "grass",
                    "Electric" => "electric",
                    "Psychic" => "psychic",
                    "Ice" => "ice",
                    "Dragon" => "dragon",
                    "Dark" => "dark",
                    "Fairy" => "fairy",

                ],
            ],
        ])
        ->add("getByType",SubmitType::class,["label"=>"Filter"])
        ->getForm();


        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $pokemon = [];
            if($form->getData()->getName() != "" ){
                foreach($pokedexRepository->getPokemon($form->getData()->getName()) as $info ){
                    $poke = [$info];
                    array_push($pokemon,$poke);
                }
            }elseif (count($form->getData()->getTypes()) > 0 ){
                foreach($pokedexRepository->getByType($form->getData()->getTypes()) as $info ){
                    $poke = [$info];
                    array_push($pokemon,$poke);
                }
            }
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

       $pokedex = new Pokedex();
        $pokedex->setTypes(["Type1"=>"","Type2"=>""]);
        $form = $this->createFormBuilder($pokedex)
        ->add("name",TextType::class,[
            "required"=>false,
        ])
        ->add("getByName",SubmitType::class,["label"=>"Search"])
        ->add("types",CollectionType::class,[
            'entry_type'=>ChoiceType::class,
            'entry_options'  => [
                'choices'  => [
                    "Select" => null,
                    "Normal" => "normal",
                    "Fighting" => "fighting",
                    "Flying" => "flying",
                    "Poison" => "poison",
                    "Ground" => "ground",
                    "Rock" => "rock",
                    "Bug" => "bug",
                    "Ghost" => "ghost",
                    "Steel" => "steel",
                    "Fire" => "fire",
                    "Water" => "water",
                    "Grass" => "grass",
                    "Electric" => "electric",
                    "Psychic" => "psychic",
                    "Ice" => "ice",
                    "Dragon" => "dragon",
                    "Dark" => "dark",
                    "Fairy" => "fairy",

                ],
            ],
        ])
        ->add("getByType",SubmitType::class,["label"=>"Filter"])
        ->setAction($this->generateUrl("app_pokedex"))
        ->getForm();
        


        $form->handleRequest($request);

        return $this->render('pokemon/getInfo.html.twig',[
            "pokemon" => $pokemon,
            "form" => $form
        ]);
    }
    

    #[Route('/pokemon/teams')]
    public function teams(PokedexRepository $pokedexRepository,Request $request):Response{
        
        $pokedex = new Pokedex();
        $form = $this->createFormBuilder($pokedex)
        ->add("name",TextType::class,[

        ])
        ->add("getPokemon",SubmitType::class,["label"=>"Search"])
        ->getForm();

        $form->handleRequest($request);

        $pokemon = ($form->isSubmitted() ) ? $pokedexRepository->getPokemon($form->getData()->getName()) : null;
        
        $team = new Team();
        $team->setMoves(["move1"=>"","move2"=>"","move3"=>"","move4"=>""]);
        $Moves = $pokemon!= null ? $pokemon[0]->getMoves() : null;
        $MoveChoices = ["Select a Move"=>""];
        if ($pokemon != null){
        foreach($Moves as $Move){
            $MoveChoices[ucfirst(str_replace("-"," ",key($Move)))] = "";
        }
    }



        $Abilities = $pokemon!= null ? $pokemon[0]->getAbilities() : null;
        $AbilityChoice = [];
        if ($pokemon != null){
        foreach($Abilities as $Ability){
            $AbilityChoice[ucfirst($Ability["name"])] = $Ability["name"];
        }
    }
        $team->setStats(["Hp"=>0,"Attack"=>0,"Defense"=>0,"Special_Attack"=>0,"Special_Defense"=>0,"Speed"=>0]);
        $teamForm = $this->createFormBuilder($team)
        ->add("moves",CollectionType::class,[
            'entry_type'=>ChoiceType::class,
            'entry_options' =>[
                'choices'=>$MoveChoices,
            ]
        ])
        ->add("stats",CollectionType::class,[
            'entry_type'=>NumberType::class,
                'attr' => [
                    'min'=>0,
                    "max" => 255,
                ],
                
            
        ])
        ->add("nature",ChoiceType::class,[
                    'choices' => [
                        "Adamant (+At,-SpA)" => "Adamant",
                        "Bashful" => "Bashful",
                        "Bold (+Def,-At)" => "Bold",
                        "Brave (+At,-Sp)" => "Brave",
                        "Calm (+SpD,-At)" => "Calm",
                        "Careful (+SpD,-SpA)" => "Careful",
                        "Docile" => "Docile",
                        "Gentle (+SpD,-Def)" => "Gentle",
                        "Hardy" => "Hardy",
                        "Hasty (+Sp,-Def)" => "Hasty",
                        "Impish (+Def,-Spa)" => "Impish",
                        "Jolly (+Sp,-SpA)" => "Jolly",
                        "Lax (+Def,-SpD)" => "Lax",
                        "Lonely (+At,-Def)" => "Lonely",
                        "Mild (+SpA,-Def)" => "Mild",
                        "Modest (+SpA,-At)" => "Modest",
                        "Naive (+Spe,-SpD)" => "Naive",
                        "Naughty (+At,-SpD)" => "Naughty",
                        "Quiet (+SpA,-Sp)" => "Quiet",
                        "Quirky" => "Quirky",
                        "Rash (+SpA,-SpD)" => "Rash",
                        "Relaxed (+Def,-Sp)" => "Relaxed",
                        "Sassy (+SpD,-Sp)" => "Sassy",
                        "Serious" => "Serious",
                        "Timid (+Sp,-At)" => "Timid",
                   ]
        ])
        ->add("ability",ChoiceType::class,[
                'choices'=> $AbilityChoice,
        ])
        ->add("addPokemon",SubmitType::class,["label"=>"Add Pokemon"])
        ->getForm();

        $teamForm->handleRequest($request);

        if($teamForm->isSubmitted() && $teamForm->get("addPokemon")->isClicked()){
            dd($teamForm->getData());
            $Team = new Team();
            $Team->setAbility($teamForm->getData()->getAbility());
            $Team->setMoves($teamForm->getData()->getMoves());
            $Team->setNature($teamForm->getData()->getNature());
            $Team->setStats($teamForm->getData()->getStats());
            $Team->setPokemonName($pokemon);
            dd($Team);
        }

        return $this->render('pokemon/teams.html.twig',[
            "pokemon" => $pokemon,
            "form" => $form,
            "TeamForm" => $teamForm,
            "Moves" => $Moves
        ]);
    }


}


?>