<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authorization;

use Chubbyphp\Security\UserInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class RoleAuthorization implements AuthorizationInterface
{
    /**
     * @var array
     */
    private $roleHierarchyResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RoleHierarchyResolverInterface $roleHierarchyResolver
     */
    public function __construct(RoleHierarchyResolverInterface $roleHierarchyResolver, LoggerInterface $logger = null)
    {
        $this->roleHierarchyResolver = $roleHierarchyResolver;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param UserInterface                  $user
     * @param mixed                          $attributes
     * @param OwnedByUserModelInterface|null $model
     *
     * @return bool
     */
    public function isGranted(UserInterface $user, $attributes, OwnedByUserModelInterface $model = null): bool
    {
        if (null !== $model && $user->getId() !== $model->getOwnedByUserId()) {
            $this->logger->info(
                'security.authorization.role: user and model owner are not the same {userId}, {ownerByUserId}',
                ['userId' => $user->getId(), 'ownerByUserId' => $model->getOwnedByUserId()]
            );

            return false;
        }

        $owningRoles = $this->getOwningRoles($user);
        $neededRoles = $this->getNeededRoles($attributes);

        $granted = $this->checkRoles($owningRoles, $neededRoles);

        $this->logger->info(
            'security.authorization.role: user {userId} granted {granted} for needed roles {neededRoles}',
            ['userId' => $user->getId(), 'granted' => $granted, 'neededRoles' => implode(', ', $neededRoles)]
        );

        return $granted;
    }

    /**
     * @param UserInterface $user
     *
     * @return array
     */
    private function getOwningRoles(UserInterface $user): array
    {
        return $this->roleHierarchyResolver->resolve($user->getRoles());
    }

    /**
     * @param mixed $attributes
     *
     * @return array
     */
    private function getNeededRoles($attributes): array
    {
        return is_scalar($attributes) ? [$attributes] : $attributes;
    }

    /**
     * @param array $owningRoles
     * @param array $neededRoles
     *
     * @return bool
     */
    private function checkRoles(array $owningRoles, array $neededRoles): bool
    {
        foreach ($neededRoles as $neededRole) {
            if (!in_array($neededRole, $owningRoles, true)) {
                return false;
            }
        }

        return true;
    }
}
