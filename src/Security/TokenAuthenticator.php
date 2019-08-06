<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;

// use Symfony\Component\Security\Core\User\UserInterface;

class TokenAuthenticator extends JWTTokenAuthenticator
{
    /**
     * Undocumented function
     * @param PreAuthenticationJWTUserToken $preAuthToken Implementation of the (Security) TokenInterface 
     * @param UserProviderInterface $userProvider
     * @return null|\Symfony\Component\Security\Core\User\UserInterface|void
     */
    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
        /** @var User $user */
        $user = parent::getUser(
            $preAuthToken,
            $userProvider
        );

        // dd($preAuthToken->getPayload());

        if ($user->getPasswordChangeDate() && $preAuthToken->getPayload()['iat'] < $user->getPasswordChangeDate()) {
            throw new ExpiredTokenException();
        }

        return $user;
    }
}
