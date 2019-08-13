<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Exception\EmptyBodyException;

class EmptyBodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST =>  [
                'handleEmptyBody',
                EventPriorities::PRE_WRITE
                // EventPriorities::PRE_VALIDATE
                // EventPriorities::POST_DESERIALIZE
                // EventPriorities::PRE_DESERIALIZE
            ]
        ];
    }

    public function handleEmptyBody(RequestEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();
        $route = $request->get('_route');

        if (
            !in_array(
                $method,
                [
                    Request::METHOD_POST,
                    Request::METHOD_PUT
                ]
            ) ||
            in_array(
                $request->getContentType(),
                [
                    'html',
                    'form'
                ]
            ) ||
            substr($route, 0, 3) !== "api"
        ) {
            return;
        }

        $data = $event->getRequest()->get('data');

        if (null === $data) {
            throw new EmptyBodyException();
        }
    }
}
