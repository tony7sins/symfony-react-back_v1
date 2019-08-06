<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Security\TokenGenerator;

class UserRegisterSubscriber implements EventSubscriberInterface
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /** @var TokenGenerator $tokenGenerator */
    private $tokenGenerator;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator
    ) {
        $this->passwordEncoder  = $passwordEncoder;
        $this->tokenGenerator   = $tokenGenerator;
    }
    public static function getSubscribedEvents()
    {

        return [
            KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
        ];
    }

    /**
     * Undocumented function
     *
     * @param GetResponseForControllerResultEvent $event - type of event for KernelEvents::VIEW event
     * @return boolean
     */
    public function userRegistered(GetResponseForControllerResultEvent $event)
    {

        /** @var User $user */
        $user = $event->getControllerResult();

        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || !in_array($method, [Request::METHOD_POST])) {
            return;
        }

        // So we're hashing password
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                /** @var string - plain [assword from request */
                $user->getPassword()
            )
        );

        // create a user confirmation token
        $user->setConfirmationToken(
            $this->tokenGenerator->getRundomSecureToken()
        );
    }
}
