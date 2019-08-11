<?php

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exception\InvalidConfirmationTokenException;
use Psr\Log\LoggerInterface;

class UserConfirmationService
{
    /** @var UserRepository */
    private $userRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->userRepository   = $userRepository;
        $this->entityManager    = $entityManager;
        $this->logger           = $logger;
    }

    public function confirmUser(string $confirmationToken)
    {
        $this->logger->debug("fetching user by confirmation process");

        $user = $this->userRepository->findOneBy(
            ['confirmationToken' => $confirmationToken]
        );

        // User was NOT found by confirmation token
        if (!$user) {
            $this->logger->debug("User by confirmation token not found");
            throw new InvalidConfirmationTokenException();
        }

        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();

        $this->logger->debug("Confirm User by token");
    }
}
