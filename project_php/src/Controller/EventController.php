<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Service\MailService;
use App\Form\EventType;
use App\Repository\EventRepository;
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

    #[Route('/events/{id}', name: 'detail_event')]
    public function detailEvent(Event $event): Response
    {
        return $this->render('detail.html.twig', [
            'event' => $event,
        ]);
    }
    #[Route('/event/{id}/register', name: 'event_register')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function register(Event $event, EntityManagerInterface $entityManager, MailService $mailService): Response
    {
        $user = $this->getUser();
        if ($event->getParticipantCount() > count($event->getParticipants())) {
            $event->addParticipant($user);
            $entityManager->persist($event);
            $entityManager->flush();

            $mailService->sendRegistrationConfirmation($user->getEmail());
        }

        return $this->redirectToRoute('detail_event', ['id' => $event->getId()]);
    }

    #[Route('/event/{id}/unregister', name: 'event_unregister')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function unregister(Event $event, EntityManagerInterface $entityManager, MailService $mailService): Response
    {
        $user = $this->getUser();
        $event->removeParticipant($user);
        $entityManager->persist($event);
        $entityManager->flush();

        $mailService->sendCancellationConfirmation($user->getEmail());

        return $this->redirectToRoute('detail_event', ['id' => $event->getId()]);
    }
}
