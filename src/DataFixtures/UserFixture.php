<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public const USER_USER_REFERENCE = 'user-user';
    public const USER_ADMIN_REFERENCE = 'user-admin';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $userAdmin = new User();
        $userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, 'admin'));
        $userAdmin->setCanReserve(true);
        $userAdmin->setDisplayname("Admin");
        $userAdmin->setEmail("admin@example.com");
        $userAdmin->setRoles(["ROLE_ADMIN"]);

        $userBasic = new User();
        $userBasic->setPassword($this->passwordHasher->hashPassword($userBasic, 'basic'));
        $userBasic->setCanReserve(false);
        $userBasic->setDisplayname("Basic");
        $userBasic->setEmail("test@example.com");
        $userBasic->setRoles(["ROLE_USER"]);

        $this->addReference(self::USER_ADMIN_REFERENCE, $userAdmin);
        $this->addReference(self::USER_USER_REFERENCE, $userBasic);

        $manager->persist($userAdmin);
        $manager->persist($userBasic);

        $manager->flush();
    }
}
