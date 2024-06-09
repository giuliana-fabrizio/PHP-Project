<?php

namespace App\Service;

use App\Entity\Event;

class RemainingPlacesService
{
    public function calculateRemainingPlaces(Event $event): int
    {
        $totalPlaces = $event->getParticipantCount();
        $registeredParticipants = count($event->getParticipants());

        return $totalPlaces - $registeredParticipants;
    }
}
