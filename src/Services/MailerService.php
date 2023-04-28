<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService


{
    private $mailer;


    public function __construct(MailerInterface $mailer, )
    {
        $this->mailer = $mailer;

    }

    public function sendNotification(): void
    {
        $toAddress='nourmoutii@gmail.com';
        $email = (new Email())
            ->from('nourtest09@gmail.com')
            ->to($toAddress)
            ->subject('une nouvelle actualité est la !')
            ->text('une nouvelle actualité est sortie ! consultez la vite !');

        $this->mailer->send($email);
    }

}