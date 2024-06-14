<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Event;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 50; $i++) {
            $event = new Event();
            $event->setTitle('Event ' . $i);
            $event->setDescription('Description for Event ' . $i);
            $event->setDatetimeStart(new \DateTime('now + ' . $i . ' days'));
            $event->setDatetimeEnd(new \DateTime('now + ' . ($i + 1) . ' days'));
            $event->setParticipantCount(mt_rand(1, 100));
            $event->setIsPublic((bool)mt_rand(0, 1));
            $price = mt_rand(0, 1) ? mt_rand(0, 100) : 0;
            $event->setPrice($price);


            $userReference = $this->getReference('user_' . ($i % 3));
            $event->setCreator($userReference);

            $manager->persist($event);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}