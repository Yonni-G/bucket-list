<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Générer 10 souhaits fictifs
        for ($i = 1; $i <= 20; $i++) {
            $wish = new Wish();
            $wish->setTitle($faker->word(5));
            $wish->setAuthor($faker->name());
            $wish->setDescription($faker->sentence(5));
            $wish->setDateCreated($faker->dateTimeBetween('-3 months', 'now'));
            $wish->setIsPublished($faker->boolean());

            // Persister l'entité (préparer pour insertion)
            $manager->persist($wish);
        }

        // Appliquer les changements dans la base de données
        $manager->flush();
    }
}

