<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Event;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {
            $event = new Event();
            $event->setTitle('Event ' . $i);
            $event->setDescription('Description for Event ' . $i);
            $event->setDatetime(new \DateTime('now + ' . $i . ' days'));
            $event->setParticipantCount(mt_rand(1, 100));
            $event->setPublic((bool)mt_rand(0, 1));

            $manager->persist($event);
        }

        $manager->flush();
    }
}
