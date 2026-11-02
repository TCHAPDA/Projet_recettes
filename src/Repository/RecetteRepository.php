<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recette>
 */
class RecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recette::class);
    }

    //    /**
    //     * @return Recette[] Returns an array of Recette objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recette
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // Version SQL natif
    // public function les10firstrecettesSql(): array
    // {
    //     $conn = $this->getEntityManager()->getConnection();
    //     $sql = 'SELECT r.*, i.* FROM recette r 
    //             JOIN recette_ingredient ri ON r.id = ri.recette_id 
    //             JOIN ingredient i ON i.id = ri.ingredient_id 
    //             LIMIT 10';
                
    //     $resultSet = $conn->executeQuery($sql);
    //     return $resultSet->fetchAllAssociative();
    // }

    // Version DQL (avec QueryBuilder et join)
    public function les10firstrecettesDql(): array
    {
        //ne pouvant pas afficher directement on recupere d'abord les 10 premiers ids de recette
        $subQuery = $this->createQueryBuilder('r')
        ->select('r.id')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
        
        $ids = array_map(fn($item)=> $item['id'], $subQuery);
        if(empty($ids)){
            return[];
        }

        return $this->createQueryBuilder('r')
            ->innerJoin('r.ingredients', 'i')
            ->addSelect('i')
            ->where('r.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

}
