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
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Créez et authentifiez un utilisateur de test
        $user = new User();
        $user->setEmail('testuserrr@example.com');
        $user->setNom('Doe');
        $user->setPrenom('John');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);

        
        $passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);
        $hashedPassword = $passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        // Test visualiser le profil
        $crawler = $client->request('GET', '/profile/edit');
        $this->assertResponseIsSuccessful();

        // Remplir le formulaire de profil
        $profileForm = $crawler->selectButton('Save')->form([
            'user_profile[nom]' => 'UpdatedDoe',
            'user_profile[prenom]' => 'UpdatedJohn',
            'user_profile[email]' => 'updated@example.com',
        ]);
        $client->submit($profileForm);

        $this->assertResponseRedirects('/profile');
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-success', 'Profile updated successfully');

        // Remplir le formulaire de mot de passe
        $crawler = $client->request('GET', '/profile/edit');
        $passwordForm = $crawler->selectButton('Change Password')->form([
            'change_password[plainPassword][first]' => 'newpassword',
            'change_password[plainPassword][second]' => 'newpassword',
        ]);
        $client->submit($passwordForm);

        $this->assertResponseRedirects('/profile');
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-success', 'Password changed successfully');

        // Vérifiez le changement de mot de passe
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'updated@example.com']);
        $this->assertTrue($passwordHasher->isPasswordValid($user, 'newpassword'));
    }
}
