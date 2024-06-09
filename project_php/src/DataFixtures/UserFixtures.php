<?php

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
        $users = [
            ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean.dupont@example.com', 'password' => 'password1'],
            ['nom' => 'Martin', 'prenom' => 'Marie', 'email' => 'marie.martin@example.com', 'password' => 'password2'],
            ['nom' => 'Durand', 'prenom' => 'Pierre', 'email' => 'pierre.durand@example.com', 'password' => 'password3'],
        ];

        foreach ($users as $key => $userData) {
            $user = new User();
            $user->setNom($userData['nom']);
            $user->setPrenom($userData['prenom']);
            $user->setEmail($userData['email']);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));
            
            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo $error->getMessage() . "\n";
                }
                return;
            }

            $manager->persist($user);
            $this->addReference('user_' . $key, $user);
        }

        $manager->flush();
    }
}