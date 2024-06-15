<?php

namespace App\Controller;

use App\Entity\Event;
use App\Security\Voter\EventVoter;
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
use Knp\Component\Pager\PaginatorInterface;

class EventController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private EntityManagerInterface $entityManager,
        private RemainingPlacesService $remainingPlacesService,
        private PaginatorInterface $paginator
    ) {
    }

    #[Route('/', name: 'app_index')]
    public function index(Request $request): Response
    {
        $events = $this->eventRepository->findAvailables();

        return $this->renderEvents($events, $request);
    }

    #[Route('/events', name: 'app_events')]
    public function getEvents(Request $request): Response
    {
        $events = $this->eventRepository->findAll();

        return $this->renderEvents($events, $request);
    }

    #[Route('/event_filter', name: 'app_event_filter')]
    public function filterEvents(Request $request): Response
    {
        $name = $request->query->get('name');
        $date_start = $request->query->get('date_start');
        $date_end = $request->query->get('date_end');
        $isPublic = $request->query->get('isPublic');

        $events = $this->eventRepository->findByFilters($name, $date_start, $date_end, $isPublic);

        return $this->renderEvents($events, $request);
    }

    public function renderEvents(array $events, Request $request): Response
    {
        $events = array_filter($events, function ($event) {
            return $this->isGranted(EventVoter::VIEW, $event);
        });

        foreach ($events as $event) {
            $event->setDescription($this->truncatedInstructions($event->getDescription()));
            $event->remainingPlaces = $this->remainingPlacesService->calculateRemainingPlaces($event);
        }

        $pagination = $this->paginator->paginate(
            $events,
            $request->query->getInt('page', 1), 
            5
        );

        return $this->render('event/list.html.twig', ['pagination' => $pagination]);
    }

    public function truncatedInstructions(string $instructions): string
    {
        $maxLength = 50;
        if (strlen($instructions) > $maxLength) {
            return substr($instructions, 0, $maxLength) . '...';
        }
        return $instructions;
    }

    #[Route('/event/{id}', name: 'app_detail_event')]
    public function detailEvent(Event $event): Response
    {
        
        if ($this->getUser() === $event->getCreator()) {
            return $this->render('event/details.html.twig', [
                'event' => $event,
                'remainingPlaces' => $this->remainingPlacesService->calculateRemainingPlaces($event),
                'showParticipantsLink' => true, 
            ]);
        }

        if (!$this->isGranted(EventVoter::VIEW, $event)) {
            $this->addFlash('danger', "Vous n'avez pas la permission d'accéder à cet évènement.");
            return $this->redirectToRoute('app_index');
        }

        return $this->render('event/details.html.twig', [
            'event' => $event,
            'remainingPlaces' => $this->remainingPlacesService->calculateRemainingPlaces($event),
        ]);
    }

    #[Route('/event/{id}/register', name: 'app_event_register')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function register(Event $event, MailService $mailService): Response
    {
        // $this->denyAccessUnlessGranted('register', $event);

        $user = $this->getUser();
        if ($event->getParticipantCount() > count($event->getParticipants())) {
            $event->addParticipant($user);
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            $mailService->sendRegistrationConfirmation($user->getEmail());

            if ($event->getPrice() > 0) {
                return $this->redirectToRoute('app_pay_event', ['id' => $event->getId()]);
            }
        }

        return $this->redirectToRoute('app_detail_event', ['id' => $event->getId()]);
    }

    #[Route('/event/{id}/unregister', name: 'app_event_unregister')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function unregister(Event $event, MailService $mailService): Response
    {
        $user = $this->getUser();
        $event->removeParticipant($user);
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $mailService->sendCancellationConfirmation($user->getEmail());

        return $this->redirectToRoute('app_detail_event', ['id' => $event->getId()]);
    }

    #[Route('/create_event', name: 'app_create_event')]
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

            return $this->redirectToRoute('app_events');
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/event/{id}/edit', name: 'app_event_edit')]
    // #[IsGranted('edit', subject: 'event')]
    public function editEvent(Request $request, Event $event): Response
    {
        if (!$this->isGranted(EventVoter::EDIT, $event)) {
            $this->addFlash('danger', "Vous n'avez pas la permission de modifier cet évènement.");
            return $this->redirectToRoute('app_detail_event', ['id' => $event->getId()]);
        }

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_detail_event', ['id' => $event->getId()]);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/event/{id}/delete', name: 'app_event_delete')]
    // #[IsGranted('delete', subject: 'event')]
    public function deleteEvent(Request $request, Event $event): Response
    {
        if (!$this->isGranted(EventVoter::DELETE, $event)) {
            $this->addFlash('danger', "Vous n'avez pas la permission de supprimer cet évènement.");
            return $this->redirectToRoute('app_detail_event', ['id' => $event->getId()]);
        }

        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_events');
    }

    #[Route('/event/{id}/participants', name: 'app_event_participants')]
    #[IsGranted('ROLE_ADMIN')]
    public function eventParticipants(Event $event): Response
    {
        $participants = $event->getParticipants();

        return $this->render('event/participants.html.twig', [
            'event' => $event,
            'participants' => $participants,
        ]);
    }
}
