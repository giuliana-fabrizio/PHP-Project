<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class WebhookController extends AbstractController
{
    private $stripeWebhookSecret;
    private $logger;

    public function __construct(string $stripeWebhookSecret, LoggerInterface $logger)
    {
        $this->stripeWebhookSecret = $stripeWebhookSecret;
        $this->logger = $logger;
    }

    #[Route('/webhook_stripe', name: 'stripe_webhook', methods: ['POST'])]
    public function handleStripeWebhook(Request $request, ManagerRegistry $doctrine): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $this->stripeWebhookSecret
            );
        } catch (SignatureVerificationException $e) {
            return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $paymentStatus = $paymentIntent->status; 
                $this->logger->info('Payment status: ' . $paymentStatus);
                $this->addFlash('success', "Paiement réussi");
                break;
            case 'payment_intent.failed':
                    $paymentIntent = $event->data->object;
                    $paymentStatus = $paymentIntent->status;
                    $this->logger->info('Payment failed: ' . $paymentStatus);
                    $this->addFlash('danger', "Paiement échoué");
                    break; 
            case 'payment_intent.processing':
                    $paymentIntent = $event->data->object;
                    $paymentStatus = $paymentIntent->status;
                    $this->logger->info('Payment processing: ' . $paymentStatus);
                    $this->addFlash('warning', "Paiement en cours");
                    break;
            default:
                $this->logger->info('Received unknown event type ' . $event->type);
        }

        return new Response('Webhook handled', Response::HTTP_OK);
    }
}
