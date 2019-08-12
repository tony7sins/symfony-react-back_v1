<?php

namespace App\Tests\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\EventSubscriber\AuthoredEntitySubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * AuthoredEntitySubscriberTest
 */
class AuthoredEntitySubscriberTest extends TestCase
{
    /** @test */
    public function testConfiguration()
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            [
                'getAuthenticatedUser',
                EventPriorities::PRE_WRITE
            ],
            $result[KernelEvents::VIEW]
        );
    }

    /** @@dataProvider providerSetAuthorCall */
    public function testSetAuthorCall(
        string $className,
        bool $shoulCallSetAuthor,
        string $method
    ) {

        $entityMock = $this->getEntityMock($className, $shoulCallSetAuthor);
        $tokenStorageMock = $this->getTokenStorageMock();
        $eventMock = $this->getEventMock($method, $entityMock);

        (new AuthoredEntitySubscriber($tokenStorageMock))
            ->getAuthenticatedUser($eventMock);
    }

    public function providerSetAuthorCall(): array
    {
        return [
            [BlogPost::class, true, 'POST'],
            [BlogPost::class, false, 'GET'],
            ['NonExisting', false, 'GET'],
            [Comment::class, true, 'POST']
        ];
    }

    public function testNoTokenPresent()
    {

        $tokenStorageMock = $this->getTokenStorageMock(false);
        $eventMock = $this->getEventMock('POST', new class
        { });

        (new AuthoredEntitySubscriber($tokenStorageMock))
            ->getAuthenticatedUser($eventMock);
    }

    public function getEntityMock(
        string $className,
        bool $shoulCallSetAuthor
    ) {
        $entityMock = $this
            ->getMockBuilder($className)
            ->setMethods(['setAuthor'])
            ->getMock();
        $entityMock
            ->expects($shoulCallSetAuthor ? $this->once() : $this->never())
            ->method('setAuthor');

        return $entityMock;
    }

    /** @return MockObject|TokenStorageInterface  */
    public function getTokenStorageMock(bool $hasToken = true): MockObject
    {
        $tokenMock = $this
            ->getMockBuilder(TokenInterface::class)
            ->getMockForAbstractClass();

        $tokenMock
            ->expects($hasToken ? $this->once() : $this->never())
            ->method('getUser')
            ->willReturn(new User);

        $tokenStorageMock = $this
            ->getMockBuilder(TokenStorageInterface::class)
            // calling for Abstract methods.
            // for Interfaces for instance getMock method should be used!
            ->getMockForAbstractClass();

        $tokenStorageMock
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($hasToken ? $tokenMock : null);

        return $tokenStorageMock;
    }

    /** @return MockObject|ViewEvent */
    public function getEventMock(
        string $method,
        $controllerResult
    ): MockObject {

        $requestMock = $this
            ->getMockBuilder(Request::class)
            ->getMock();
        $requestMock
            ->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $eventMock = $this
            ->getMockBuilder(ViewEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock
            ->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($controllerResult);

        $eventMock
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        return $eventMock;
    }
}
