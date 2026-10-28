<?php

namespace App\Repository;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ingredient>
 */
class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }

    //    /**
    //     * @return Ingredient[] Returns an array of Ingredient objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ingredient
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    //sql
    //tout les ingredients
    public function findAll_sql(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        // $sql = 'SELECT * FROM ingredient';
        // On sélectionne chaque colonne et on renomme la colonne de date pour correspondre à Twig
        $sql = 'SELECT id, name, price, creatd_at AS creatdAt FROM ingredient';
        
        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();
    }

    // 2. Rechercher les tomates en SQL
    public function find_ingredient_tomato_sql(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        // $sql = 'SELECT * FROM ingredient WHERE name LIKE :name';
        $sql = 'SELECT id, name, price, creatd_at AS creatdAt FROM ingredient WHERE name LIKE :name' ;
        $resultSet = $conn->executeQuery($sql, ['name' => '%tomate%']);
        return $resultSet->fetchAllAssociative();
    }

    // 3. Filtrer par prix en SQL
    public function find_ingredient_by_price_sql(float $maxPrice): array
    {
        $conn = $this->getEntityManager()->getConnection();
        // $sql = 'SELECT * FROM ingredient WHERE price < :price ORDER BY price ASC';
        $sql = 'SELECT id, name, price, creatd_at AS creatdAt FROM ingredient WHERE price < :price' ;

        $resultSet = $conn->executeQuery($sql, ['price' => $maxPrice]);
        return $resultSet->fetchAllAssociative();
    }



    //creation de la fonction findall
    public function findAll_dql(): array
    {
        $entityManager = $this->getEntityManager();
        
        //utilisation de create query (DQL)
        $query = $entityManager->createQuery(
            'SELECT i FROM App\Entity\Ingredient i'
        );

        //retourne les resultat
        return $query->getResult();
    }

    //creation de find_tomato
    public function find_ingredient_tomato_dql(): array{
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT i FROM App\Entity\Ingredient i WHERE i.name LIKE :name'
        )->setParameter('name', '%tomate%'); //les % servent a chercher le mot tomate meme dans un mot plus long

        return $query->getResult();
    }

    public function find_ingredient_by_price_dql(float $maxPrice): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT i FROM App\Entity\Ingredient i WHERE i.price < :price ORDER BY i.price ASC'
        )->setParameter('price', $maxPrice);

    return $query->getResult();
}

}
