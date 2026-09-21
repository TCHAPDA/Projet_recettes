<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Ingredient;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 5; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($faker->word());
            $ingredient->setPrice($faker->randomFloat(2, 0, 200));
            $manager->persist($ingredient); 
        }

        $manager->flush();
    }
}
