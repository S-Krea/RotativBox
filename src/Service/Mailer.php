<?php

namespace App\Service;

use App\Model\Box;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendBox(?Box $box, $params, $results)
    {
        $message = (new TemplatedEmail())
            ->from($params['email'])
            ->to('commercial@cimedentaire.fr')
            ->subject('Nouveau devis Rotativ Box')
            ->htmlTemplate('mails/devis.html.twig')
            ->context(['box' => $box, 'params' => $params, 'result' => $results])
        ;

        $this->mailer->send($message);
    }
}