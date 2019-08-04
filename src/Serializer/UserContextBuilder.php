<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Entity\User;

class UserContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * @var SerializerContextBuilderInterface $decorator
     */
    private $decorator;

    /**
     * @var AuthorizationCheckerInterface $authorizationChecker
     */
    private $authorizationChecker;

    public function __construct(
        SerializerContextBuilderInterface $decorator,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->decorator = $decorator;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createFromRequest(
        Request $request,
        bool $normalization,
        array $extractedAttributes = null
    ): array {
        $context = $this->decorator->createFromRequest(
            $request,
            $normalization,
            $extractedAttributes
        );

        // class being serialized/deserialized
        $resourceClass = $context['resource_class'] ?? null;  // default to null if not set

        if (
            User::class === $resourceClass &&
            isset($context['groups']) &&
            $normalization === true &&
            $this->authorizationChecker->isGranted(User::ROLE_ADMIN)
        ) {
            $context['groups'][] = 'get-admin';
        }

        return $context;
    }
}