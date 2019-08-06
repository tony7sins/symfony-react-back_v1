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

class PasswordHashSubscriber implements EventSubscriberInterface
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['hashPassword', EventPriorities::PRE_WRITE]
        ];
    }

    /**
     * Undocumented function
     *
     * @param GetResponseForControllerResultEvent $event - type of event for KernelEvents::VIEW event
     * @return boolean
     */
    public function hashPassword(GetResponseForControllerResultEvent $event)
    {
        /** @var UserInterface $user */
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
    }
}
