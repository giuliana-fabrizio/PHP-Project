<?php
namespace App\Controller;

use App\Repository\EventRepository;
use App\Service\RemainingPlacesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private EntityManagerInterface $entityManager,
        private RemainingPlacesService $remainingPlacesService
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $events = $this->eventRepository->findAvailables();

        foreach ($events as $event) {
            $event->remainingPlaces = $this->remainingPlacesService->calculateRemainingPlaces($event);
        }

        return $this->render('event/list.html.twig', [
            'events' => $events
        ]);
    }
}