<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProfileControllerTest extends WebTestCase
{
    public function testEditProfile(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        // Créez et authentifiez un utilisateur de test
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setNom('Doe');
        $user->setPrenom('John');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);

        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $hashedPassword = $passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        $crawler = $client->request('GET', '/profile/edit');
        $this->assertResponseIsSuccessful();


        // Remplir le formulaire de profil
        $profileForm = $crawler->selectButton('Valider')->form([
            'user_profile[email]' => 'updated@example.com',
            'user_profile[prenom]' => 'UpdatedJohn',
            'user_profile[nom]' => 'UpdatedDoe',
        ]);
        $client->submit($profileForm);

        // TEST AU DESSUS COMPLET MARCHEEEEE

        $this->assertResponseRedirects('/profile');
        $client->followRedirect();
        $this->assertSelectorTextContains('.toast-body', 'Profile updated successfully');

        $crawler = $client->request('GET', '/profile/edit/password');
        $passwordForm = $crawler->selectButton('Modifier mot de passe')->form([
            'change_password[plainPassword][first]' => 'newpassword',
            'change_password[plainPassword][second]' => 'newpassword',
        ]);
        $client->submit($passwordForm);

        $this->assertResponseRedirects('/profile');
        $client->followRedirect();
        $this->assertSelectorTextContains('.toast-body', 'Password changed successfully');

        // // Récupérer l'utilisateur mis à jour
        // $updatedUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'updated@example.com']);
        // $this->assertTrue($passwordHasher->isPasswordValid($updatedUser, 'newpassword'));
    }
}
