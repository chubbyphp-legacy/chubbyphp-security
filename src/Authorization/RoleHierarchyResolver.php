<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authorization;

final class RoleHierarchyResolver implements RoleHierarchyResolverInterface
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
     * @param array $roles
     *
     * @return array
     */
    public function resolve(array $roles): array
    {
        return $this->resolveRoleHierarchy($roles);
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

        sort($roles);

        return array_unique($roles);
    }
}
