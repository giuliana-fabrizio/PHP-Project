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
        $this->sendEmail($to, 'Confirmation d\'inscription', 'Vous êtes inscrit à l\'événement.');
    }

    public function sendCancellationConfirmation(string $to): void
    {
        $this->sendEmail($to, 'Confirmation d\'annulation', 'Vous avez annulé votre inscription à l\'événement.');
    }
}
