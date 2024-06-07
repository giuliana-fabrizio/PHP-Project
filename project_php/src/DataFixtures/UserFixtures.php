<?php

// src/DataFixtures/UserFixtures.php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setNom('Dupont');
        $user1->setPrenom('Jean');
        $user1->setEmail('jean.dupont@example.com');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password1'));

        $errors = $this->validator->validate($user1);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo $error->getMessage()."\n";
            }
            return;
        }

        $manager->persist($user1);

        $user2 = new User();
        $user2->setNom('Martin');
        $user2->setPrenom('Marie');
        $user2->setEmail('marie.martin@example.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password2'));

        $errors = $this->validator->validate($user2);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo $error->getMessage()."\n";
            }
            return;
        }

        $manager->persist($user2);

        $user3 = new User();
        $user3->setNom('Durand');
        $user3->setPrenom('Pierre');
        $user3->setEmail('pierre.durand@example.com');
        $user3->setRoles(['ROLE_USER']);
        $user3->setPassword($this->passwordHasher->hashPassword($user3, 'password3'));

        $errors = $this->validator->validate($user3);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo $error->getMessage()."\n";
            }
            return;
        }

        $manager->persist($user3);

        $manager->flush();
    }
}
