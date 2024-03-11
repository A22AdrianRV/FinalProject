<?php

namespace App\Repository;

use App\Entity\Pokedex;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pokedex>
 *
 * @method Pokedex|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pokedex|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pokedex[]    findAll()
 * @method Pokedex[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PokedexRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokedex::class);
    }

       /**
        * @return Pokedex[] Returns an array of Pokedex objects
        */
       public function findByName($value): array
       {
           return $this->createQueryBuilder('p')
               ->andWhere('p.name = :val')
               ->setParameter('val', '%'.$value.'%')
               ->orderBy('p.id', 'ASC')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult();
       }

       // This is a simple query based on name 
       public function getPokemon($name):array{
            return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :val')
            ->setParameter("val",'%'.$name.'%')
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();
       }

       // This function filters by typem it also checks whethere there are 2 types or just one 
       public function getByType($arr):array{

        if($arr["Type2"]==null){
            return $this->createQueryBuilder('p')
            ->andWhere('p.types LIKE :val')
            ->setParameter("val",'%'.$arr["Type1"] . '%')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
        }else{
            return $this->createQueryBuilder('p')
            ->andWhere('p.types LIKE :val')
            ->andWhere('p.types LIKE :val2')
            ->setParameter("val",'%'.$arr["Type1"] . '%')
            ->setParameter("val2",'%'.$arr["Type2"] . '%')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
        }

       }
    //    public function findOneBySomeField($value): ?Pokedex
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
