<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $PasswordHasher;
    public function __construct(UserPasswordHasherInterface $PasswordHasher)
    {
        $this->PasswordHasher = $PasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $plainPassword = "admin";
        $PasswordHasher = $this->PasswordHasher->hashPassword($user, $plainPassword); 
        $user->setUsername('admin');
        $user->setPassword($PasswordHasher);
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
