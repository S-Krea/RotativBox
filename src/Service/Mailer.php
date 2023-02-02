<?php

namespace App\Service;

use App\Model\Box;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer, string $to)
    {
        $this->mailer = $mailer;
        $this->to = $to;
    }

    public function sendBox(?Box $box, $params, $results)
    {
        $message = (new TemplatedEmail())
            ->from($params['email'])
            ->to($this->to)
            ->subject('Nouveau devis Rotativ Box')
            ->htmlTemplate('mails/devis.html.twig')
            ->context(['box' => $box, 'params' => $params, 'result' => $results])
        ;

        $this->mailer->send($message);
    }
}