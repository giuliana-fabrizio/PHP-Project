<?php

namespace App\Tests\Service;

use App\Service\MailService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailServiceTest extends TestCase
{
    public function testSendRegistrationConfirmation()
    {
        $mailerMock = $this->createMock(MailerInterface::class);
        $mailerMock->expects($this->once())
                   ->method('send')
                   ->with($this->callback(function (Email $email) {
                        return $email->getTo()[0]->getAddress() === 'test@example.com' &&
                               $email->getSubject() === 'Confirmation d\'inscription' &&
                               $email->getTextBody() === 'Vous êtes inscrit à l\'évènement.';
                   }));

        $mailService = new MailService($mailerMock);
        $mailService->sendRegistrationConfirmation('test@example.com');
    }

    public function testSendCancellationConfirmation()
    {
        $mailerMock = $this->createMock(MailerInterface::class);
        $mailerMock->expects($this->once())
                   ->method('send')
                   ->with($this->callback(function (Email $email) {
                        return $email->getTo()[0]->getAddress() === 'test@example.com' &&
                               $email->getSubject() === 'Confirmation d\'annulation' &&
                               $email->getTextBody() === 'Vous avez annulé votre inscription à l\'évènement.';
                   }));

        $mailService = new MailService($mailerMock);
        $mailService->sendCancellationConfirmation('test@example.com');
    }
}
