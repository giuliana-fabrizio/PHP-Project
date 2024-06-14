<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $text): void
    {
        $email = (new Email())
            ->from('matheogigi28@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($text);

        $this->mailer->send($email);
    }

    public function sendRegistrationConfirmation(string $to): void
    {
        $this->sendEmail($to, 'Confirmation d\'inscription', 'Vous êtes inscrit à l\'évènement.');
    }

    public function sendCancellationConfirmation(string $to): void
    {
        $this->sendEmail($to, 'Confirmation d\'annulation', 'Vous avez annulé votre inscription à l\'évènement.');
    }
    public function sendPaymentSuccessConfirmation(string $to): void
    {
        $this->sendEmail($to, 'Confirmation de paiement', 'Votre paiement a été effectué avec succès.');
    }
    public function sendPaymentFailure(string $to): void
    {
        $this->sendEmail($to, 'Paiement echoué', 'Votre paiement a été echoué.');
    }
}
