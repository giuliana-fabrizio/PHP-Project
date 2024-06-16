<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    private MailService $mailService;

    public function __construct(private EntityManagerInterface $entityManager, MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    #[Route(path: '/pay_event/{id}', name: 'app_pay_event')]
    public function checkout(Event $event, string $stripeSK): Response
    {
        $stripe = new \Stripe\StripeClient($stripeSK);

        $priceInCents = (int) round($event->getPrice() * 100);

        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $event->getTitle(),
                    ],
                    'unit_amount' => $priceInCents,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', ['id' => $event->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($checkout_session->url, 303);
    }

    #[Route(path: '/success-url', name: 'success_url')]
    public function successUrl(): Response
    {
        $this->mailService->sendPaymentSuccessConfirmation($this->getUser()->getEmail());
        return $this->render('payment/success.html.twig', []);
    }

    #[Route(path: '/cancel-url/{id}', name: 'cancel_url')]
    public function cancelUrl(Event $event): Response
    {
        $user = $this->getUser();
        $event->removeParticipant($user);
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $this->mailService->sendPaymentFailure($this->getUser()->getEmail());
        return $this->render('payment/cancel.html.twig', [
            'event' => $event,
        ]);
    }
}
