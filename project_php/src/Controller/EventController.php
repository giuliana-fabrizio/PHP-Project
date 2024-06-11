<?php

namespace App\Controller;

use App\Entity\Event;
use App\Service\MailService;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\RemainingPlacesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private EntityManagerInterface $entityManager,
        private RemainingPlacesService $remainingPlacesService
    ) {}

    #[Route('/create_event', name: 'create_event')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function createEvent(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setCreator($this->getUser());
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            return $this->redirectToRoute('events');
        }

        return $this->render('create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/events', name: 'events')]
    public function getEvents(): Response
    {
        $events = $this->eventRepository->findAll();

        foreach ($events as $event) {
            $event->remainingPlaces = $this->remainingPlacesService->calculateRemainingPlaces($event);
        }

        return $this->render('event_list.html.twig', [
            'events' => $events
        ]);
    }

    #[Route('/event_filter', name: 'event_filter')]
    public function filterEvents(Request $request): Response
    {
        $name = $request->query->get('name');
        $date_start = $request->query->get('date_start');
        $date_end = $request->query->get('date_end');
        $isPublic = $request->query->get('isPublic');

        $events = $this->eventRepository->findByFilters($name, $date_start, $date_end, $isPublic);

        return $this->render('event_list.html.twig', ['events' => $events]);
    }

    #[Route('/events/{id}', name: 'detail_event')]
    public function detailEvent(Event $event): Response
    {
        $remainingPlaces = $this->remainingPlacesService->calculateRemainingPlaces($event);

        return $this->render('detail.html.twig', [
            'event' => $event,
            'remainingPlaces' => $remainingPlaces
        ]);
    }

    #[Route('/event/{id}/register', name: 'event_register')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function register(Event $event, MailService $mailService): Response
    {
        $this->denyAccessUnlessGranted('register', $event);

        $user = $this->getUser();
        if ($event->getParticipantCount() > count($event->getParticipants())) {
            $event->addParticipant($user);
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $mailService->sendRegistrationConfirmation($user->getEmail());
        }

        return $this->redirectToRoute('detail_event', ['id' => $event->getId()]);
    }

    #[Route('/event/{id}/unregister', name: 'event_unregister')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function unregister(Event $event, MailService $mailService): Response
    {
        $user = $this->getUser();
        $event->removeParticipant($user);
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $mailService->sendCancellationConfirmation($user->getEmail());

        return $this->redirectToRoute('detail_event', ['id' => $event->getId()]);
    }

    #[Route('/event/{id}/edit', name:'event_edit')]
    #[IsGranted('edit', subject: 'event')]
    public function editEvent(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('detail_event', ['id' => $event->getId()]);
        }

        return $this->render('edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/event/{id}/delete', name:'event_delete', methods: ['POST'])]
    #[IsGranted('delete', subject: 'event')]
    public function deleteEvent(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('events');
    }
}
