<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authorization;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class RoleHierarchyResolver implements RoleHierarchyResolverInterface
{
    /**
     * @var array
     */
    private $roleHierarchy;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param array $roleHierarchy
     */
    public function __construct(array $roleHierarchy = [], LoggerInterface $logger = null)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param array $roles
     *
     * @return array
     */
    public function resolve(array $roles): array
    {
        $resolvedRoles = array_unique($this->resolveRoleHierarchy($roles));
        sort($resolvedRoles);

        $this->logger->info(
            'security.authorization.rolehierarchyresolver: resolved roles {resolvedRoles} by given roles {roles}',
            ['resolvedRoles' => implode(', ', $resolvedRoles), 'roles' => implode(', ', $roles)]
        );

        return $resolvedRoles;
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

        return $roles;
    }
}
