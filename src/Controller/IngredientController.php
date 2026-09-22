<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use App\Entity\Ingredient;

final class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(IngredientRepository $repository): Response
    {

        // On récupère tous les ingrédients en BDD grâce au repository
        $ingredients = $repository->findAll();

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/ingredient/greater_than_100', name: 'app_ingredient_greater_than_100')]
    public function index_only_greater_than_100(IngredientRepository $repository): Response
    {
        
        $ingredients = $repository->findAll();
        $ingredients_100 = [];
        foreach ($ingredients as $ingredient) {
            if ($ingredient->getPrice() >100) {
                $ingredients_100 []= $ingredient;
            }
        }
        return $this->render('ingredient/index.html.twig', [
        'ingredients' => $ingredients_100,]);
    }

    #[Route('/ingredient/greater_than_100_v2', name: 'app_ingredient_greater_than_100_v2')]
    public function index_only_greater_than_100_v2(IngredientRepository $repository): Response
    {
        $ingredients = $repository->findAll();
        $ingredient_collection = new ArrayCollection($ingredients);

        $filterIngredientCollection = $ingredient_collection->filter(
            fn(Ingredient $ingredient) => $ingredient->getPrice() > 100
        );

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $filterIngredientCollection,]);
    }

    #[Route('/ingredient/greater_than_100_v3', name: 'app_ingredient_greater_than_100_v3')]
    public function index_only_greater_than_100_v3(IngredientRepository $repository): Response
    {
        $ingredientCollection = new ArrayCollection($repository->findAll());

        // critere de selction
        $criteria = Criteria::create()->where(Criteria::expr()->gt('price', 100));
        
        $ingredient_than_100 = $ingredientCollection->matching($criteria);

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredient_than_100,]);
    }

    #[Route('/ingredient/greater_than_100_v4', name: 'app_ingredient_greater_than_100_v4')]
    public function index_only_greater_than_100_v4(IngredientRepository $repository): Response
    {
        //on cree notre critere puis on match directement avec $repository de IngredientRepository
        $criteria = Criteria::create()->where(Criteria::expr()->gt('price', 100));
        $ingredients_100 = $repository->matching($criteria);


        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients_100,]);
    }

}
