<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Event;

class PaymentController extends AbstractController
{
    #[Route(path: '/pay_event/{id}', name: 'app_pay_event')]
    public function checkout(Event $event, $stripeSK): Response
    {
        $stripe = new \Stripe\StripeClient($stripeSK);

        $priceInCents = (int)round($event->getPrice() * 100);

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
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
          ]);

          return $this->redirect($checkout_session->url, 303);
    }

    #[Route(path: '/success-url', name: 'success_url')]
    public function successUrl(): Response
    {
        return $this->render('payment/success.html.twig', []);
    }

    #[Route(path: '/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }
}