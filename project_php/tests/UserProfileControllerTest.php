<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserProfileControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = $this->client->getContainer()->get('security.password_hasher');
    }

    public function testEditProfile(): void
    {
        $user = $this->createUser('testuser@example.com', 'John', 'Doe', 'password');

        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/edit_user');
        $this->assertResponseIsSuccessful();

        $profileForm = $crawler->selectButton('submit_edit_user')->form([
            'user_profile[email]' => 'updated@example.com',
            'user_profile[prenom]' => 'UpdatedJohn',
            'user_profile[nom]' => 'UpdatedDoe',
        ]);
        $this->client->submit($profileForm);

        $this->assertResponseRedirects('/user_profile');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.toast-body', 'Profil mis à jour avec succès');

        $crawler = $this->client->request('GET', '/edit_user_password');
        $passwordForm = $crawler->selectButton('submit_edit_password')->form([
            'change_password[plainPassword][first]' => 'newpassword123',
            'change_password[plainPassword][second]' => 'newpassword123',
        ]);
        $this->client->submit($passwordForm);

        $this->assertResponseRedirects('/user_profile');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.toast-body', 'Mot de passe mis à jour avec succès');

        $updatedUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'updated@example.com']);
        $this->assertTrue($this->passwordHasher->isPasswordValid($updatedUser, 'newpassword123'));
    }

    protected function createUser($email, $prenom, $nom, $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; 
    }
}

