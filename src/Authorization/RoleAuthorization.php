<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authorization;

use Chubbyphp\Security\UserInterface;

final class RoleAuthorization implements AuthorizationInterface
{
    /**
     * @var array
     */
    private $roleHierarchyResolver;

    /**
     * @param RoleHierarchyResolverInterface $roleHierarchyResolver
     */
    public function __construct(RoleHierarchyResolverInterface $roleHierarchyResolver)
    {
        $this->roleHierarchyResolver = $roleHierarchyResolver;
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
            return false;
        }

        $owningRoles = $this->getOwningRoles($user);
        $neededRoles = $this->getNeededRoles($attributes);

        return $this->checkRoles($owningRoles, $neededRoles);
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
