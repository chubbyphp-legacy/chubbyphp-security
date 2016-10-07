<?php

namespace Chubbyphp\Security\Authorization;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\UserInterface;

final class RoleAuthorization implements AuthorizationInterface
{
    /**
     * @var array
     */
    private $roleHierarchy;

    /**
     * @param array $roleHierarchy
     */
    public function __construct(array $roleHierarchy = [])
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @param UserInterface       $user
     * @param mixed               $attributes
     * @param ModelInterface|null $model
     *
     * @return bool
     */
    public function isGranted(UserInterface $user, $attributes, ModelInterface $model = null): bool
    {
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
        return $this->resolveRoleHierarchy($user->getRoles());
    }

    /**
     * @param array $roles
     * @param array $alreadySolvedRoles
     *
     * @return array
     */
    private function resolveRoleHierarchy(array $roles, array $alreadySolvedRoles = []): array
    {
        foreach ($roles as $role) {
            if (isset($this->roleHierarchy[$role])) {
                if (in_array($role, $alreadySolvedRoles, true)) {
                    continue;
                }

                $alreadySolvedRoles[] = $role;
                $resolveRoles = $this->resolveRoleHierarchy($this->roleHierarchy[$role], $alreadySolvedRoles);
                $roles = array_merge($roles, $resolveRoles);
            }
        }

        return array_unique($roles);
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
