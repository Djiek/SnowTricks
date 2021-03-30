<?php

namespace App\Security;

use App\Entity\User as AppUser;
use App\Security\AccountNotActivatedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if ($user->getToken()) {
            throw new AccountNotActivatedException();
        }
    }
    public function checkPostAuth(UserInterface $user)
    {
    }
}
