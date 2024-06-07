<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/create_event', name: 'create_event')]
    public function createEvent(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/events', name: 'event_list')]
    public function getEvents(): Response
    {
        return $this->render('event_list.html.twig', [
            'events' => $this->eventRepository->findAll()
        ]);
    }

    #[Route('/event_filter', name: 'event_filter')]
    public function filterEvents(Request $request): Response
    {
        $name = $request->query->get('name');
        $date = $request->query->get('date');
        $isPublic = $request->query->get('isPublic');

        $events = $this->eventRepository->findByFilters($name, $date, $isPublic);

        return $this->render('event_list.html.twig', ['events' => $events]);
    }
}
