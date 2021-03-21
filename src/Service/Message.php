<?php

namespace App\Service;

use Twig\Environment;

class Message
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function createMessage($user)
    {
        //envoie du mail
        return (new \Swift_Message('Activation de votre compte'))
            ->setFrom('justineDamory@gmail.com')
            ->setTo($user->getMail())
            ->setBody(
                $this->twig->render(
                    'security/activation.html.twig',
                    ['token' => $user->getToken()]
                ),
                'text/html'
            );
    }
}
