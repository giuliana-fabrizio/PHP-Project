<?php
namespace App\Controller;

use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $events = $this->eventRepository->findAvailables();

        return $this->render('event_list.html.twig', [
            'events' => $events
        ]);
    }
}