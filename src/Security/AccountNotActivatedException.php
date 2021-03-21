<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountNotActivatedException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Votre compte est en attente de confirmation, veuillez verifier vos mails.';
    }
}
