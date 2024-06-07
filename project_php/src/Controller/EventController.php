<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/events', name: 'event_list')]
    public function getEvents(): Response
    {
        return $this->render('event_list.html.twig', [
            'events' => $this->eventRepository->findAll()
        ]);
    }
}
