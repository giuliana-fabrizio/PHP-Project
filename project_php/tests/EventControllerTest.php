<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EventControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        
        $this->assertResponseIsSuccessful();
        
        $this->assertGreaterThan(0, $crawler->filter('.card')->count(), 'The list should contain events.');
        
        $eventItem = $crawler->filter('.card')->first();
        $this->assertNotEmpty($eventItem->filter('p')->text(), 'The event item should have a title and description.');
        $this->assertGreaterThan(0, $eventItem->filter('.badge')->count(), 'There should be badges indicating event status.');
    }

    public function testGetEvents()
    {
        $client = static::createClient();
        $client->request('GET', '/events');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.card');
    }

    public function testDetailEvent()
    {
        $client = static::createClient();
        $client->request('GET', '/event/113');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Event 1');
    }

    public function testRegister()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        if ($existingUser) {
            $entityManager->remove($existingUser);
            $entityManager->flush();
        }

        $user = new User();
        $user->setNom('Test');
        $user->setPrenom('User');
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $client->getContainer()->get('security.password_hasher')->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        $client->request('GET', '/event/113/register');
        
        $this->assertResponseRedirects('/event/113');
    }

    public function testCreateEvent()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'test1@example.com']);
        if ($existingUser) {
            $entityManager->remove($existingUser);
            $entityManager->flush();
        }

        $user = new User();
        $user->setNom('Test1');
        $user->setPrenom('User1');
        $user->setEmail('test1@example.com');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $client->getContainer()->get('security.password_hasher')->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);
        $crawler = $client->request('GET', '/create_event');
        
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Valider')->form([
            'event[title]' => 'Test Event',
            'event[description]' => 'Test Event Description',
            'event[datetime_start]' => '2024-06-16 12:19:58',
            'event[datetime_end]' => '2024-06-18 12:19:58',
            'event[participant_count]' => 10,
            'event[price]' => 20,
            'event[is_public]' => true,
        ]);
        $client->submit($form);

        $this->assertResponseRedirects('/events');
    }
}
