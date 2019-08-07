<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\UserConfirmation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
// use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Swift_Message;

class UserConfirmationSubscriber implements EventSubscriberInterface
{
    /** @var UserRepository */
    private $userRepository;

    /** @var EntityManagerInterface */
    private $em;
    /** @var \Swift_Mailer $mailer */
    private $mailer;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $em,
        \Swift_Mailer $mailer
    ) {

        $this->userRepository   = $userRepository;
        $this->em               = $em;
        $this->mailer           = $mailer;
    }
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                'userConfirm',
                EventPriorities::POST_VALIDATE
            ],
        ];
    }

    public function userConfirm(ViewEvent $event)
    {

        $request = $event->getRequest();

        if ('api_user_confirmations_post_collection' !== $request->get('_route')) {
            return;
        }

        /** @var UserConfirmation $confirmationToken */
        $confirmationToken = $event->getControllerResulT();

        $user = $this->userRepository->findOneBy(
            ['confirmationToken' => $confirmationToken->confirmationToken]
        );

        // User was found by confirmation token
        if (!$user) {
            throw new NotFoundHttpException();
        }

        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->em->flush();

        $event->setResponse(new JsonResponse(
            null,
            Response::HTTP_OK
        ));

        // Send an Email to registered user
        $message = (new Swift_Message('hello from API Platform'))
            ->setFrom('test@gmail.com')
            ->setTo('test@mail.com')
            ->setBody("Hello from API Platform");

        $this->mailer->send($message);
    }
}
