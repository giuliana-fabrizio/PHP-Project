<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\RemainingPlacesService;
use App\Entity\Event;
use App\Entity\User;

class RemainingPlacesServiceTest extends TestCase
{
    public function testCalculateRemainingPlaces()
    {
        $remainingPlacesService = new RemainingPlacesService();

        $event = new Event();
        $event->setParticipantCount(50);

        $user1 = new User();
        $user2 = new User();
        
        $event->addParticipant($user1);
        $event->addParticipant($user2);

        $remainingPlaces = $remainingPlacesService->calculateRemainingPlaces($event);

        $this->assertEquals(48, $remainingPlaces);
    }
}
