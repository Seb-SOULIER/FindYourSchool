<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendMail
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function mailComment($mail,$comment):string
    {
            $email = (new Email())
                ->from('site_FindYourSchool@cod4y.fr')
                ->to('soulier.sebastien.ss@gmail.com')
                ->subject('Commentaire sur le site')
                ->html('<h1> Message de : ' . $mail . '</h1><br><p>' . $comment . '</p>');

            $this->mailer->send($email);
        return 'ok';
    }

    public function mailDonate($mail,$amount):string
    {
        $email = (new Email())
            ->from('site_FindYourSchool@cod4y.fr')
            ->to('soulier.sebastien.ss@gmail.com')
            ->subject('Don pour le site')
            ->html('<h1> Don de : ' . $mail . ' de : ' . $amount . '€uros </h1>');

        $this->mailer->send($email);
        return 'ok';
    }
}
