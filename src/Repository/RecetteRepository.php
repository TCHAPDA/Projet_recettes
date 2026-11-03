<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

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
    //cette version n'affiche que deux ou trois recette car considre 
    // chaque ligne renvoye comme une recette a part entiere

    public function les10firstrecettesSql(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT r.*, i.* FROM recette r 
                JOIN recette_ingredient ri ON r.id = ri.recette_id 
                JOIN ingredient i ON i.id = ri.ingredient_id 
                LIMIT 10';
                
        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();
    }

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

    //recette avec 5 ingredient
    public function recettes5ingredientsDql(): array{
        return $this-> createQueryBuilder('r')
        ->join('r.ingredients', 'i')
        ->groupBy('r.id')
        ->having('COUNT(i.id) = 5')
        ->getQuery()
        ->getResult();
    }

    public function recettes5ingredientsSql(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM recette WHERE id IN (
            SELECT recette_id FROM recette_ingredient GROUP BY recette_id
            HAVING COUNT(ingredient_id) = 5)';

        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();
    }

    //recup de toutes les recettes
    public function recettetingredientsDql(): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.ingredients', 'i')
            ->addSelect('i')
            ->getQuery()
            ->getResult();
    }

}
