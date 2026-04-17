<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // --- Création d'un User ---
        $user = new User();
        $user->setEmail($faker->unique()->safeEmail());
        $user->setFirstName($faker->firstName());
        $user->setLastName($faker->lastName());
        $user->setUsername($faker->unique()->userName());
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setIsActive($faker->boolean(80)); // 80% de chance d'être actif
        $user->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 years', '-1 month')));
        $user->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', 'now')));
        $user->setProfilePicture($faker->imageUrl(200, 200, 'people'));

        $manager->persist($user);

        // --- Création d'un Post lié à ce User ---
        $post = new Post();
        $post->setTitle($faker->sentence(6));
        $post->setContent($faker->paragraphs(4, true));
        $post->setPublishedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
        $post->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', '-6 months')));
        $post->setPicture($faker->imageUrl(800, 600, 'nature'));
        $post->setUser($user);  // lié au user créé juste avant

        $manager->persist($post);

        $manager->flush();
    }
}