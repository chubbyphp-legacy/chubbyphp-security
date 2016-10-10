<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authorization;

interface RoleHierarchyResolverInterface
{
    /**
     * @param array $roles
     *
     * @return array
     */
    public function resolve(array $roles): array;
}
