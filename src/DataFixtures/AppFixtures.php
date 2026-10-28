<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        //creation de 100 ingredients
        $ingredients =  [];
        for ($i = 1; $i <= 100; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName('ingr_' . $faker->unique()->word());
            $ingredient->setPrice($faker->randomFloat(2, 0.5, 200));

            $manager->persist($ingredient);
            $ingredients[]= $ingredient; //on stock pour lier aux recettes
        }

        //creation des 50 recettes
        for ($i=1; $i<=50; $i++){
            $recette = new Recette();
            $recette->setNom($faker->sentence(3));
            $recette->setTemps($faker->numberBetween(10, 120)); // entre 10 et 120 minutes
            $recette->setDescription($faker->text(200));
            $recette->setPrix($faker->randomFloat(2, 5, 50));
            $recette->setDifficulte($faker->numberBetween(0, 5));

            // Ajout de 2 à 10 ingrédients aléatoires par recette
            // On utilise la méthode magique générée par la relation ManyToMany (addIngredient)
            $randomIngredients = $faker->randomElements($ingredients, $faker->numberBetween(2, 10));
            foreach ($randomIngredients as $ingredient) {
                $recette->addIngredient($ingredient);
            }

            $manager->persist($recette);
        }

        $manager->flush();
    }
}
