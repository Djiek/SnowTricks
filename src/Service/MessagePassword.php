<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MessagePassword
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function createMessagePassword($user, $theToken)
    {
        $url = $this->router->generate(
            'app_reset_password',
            ['token' => $theToken],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return (new \Swift_Message('Mot de passe oublié'))
            ->setFrom('justineDamory@gmail.com')
            ->setTo($user->getMail())
            ->setBody(
                "<p>Bonjour,</p>
                <p> Une demande de réinitialisation de mot de passe a été effectuée pour le site SnowTricks.
                 Veuillez cliquer sur ce lien pour reinitaliser votre mot de paasse. : " . $url . '</p>',
                'text/html'
            );
    }
}
