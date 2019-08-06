<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\DisabledException;

class UserEnabledChecker implements UserCheckerInterface
{
    /**
     * Undocumented function
     *
     * @param UserInterface $user
     * @return void
     * @throws AccountStatusException
     */
    public function checkPreAuth(UserInterface $user)
    {

        if (!$user instanceof User) {
            return;
        }

        if (!$user->getEnabled()) {
            throw new DisabledException();
        }
    }

    public function checkPostAuth(UserInterface $user)
    { }
}
