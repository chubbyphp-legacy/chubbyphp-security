<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authorization;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class AuthorizationProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $this->registerRoleHierarchyResolver($container);

        $container['security.authorization.authorizations'] = function () use ($container) {
            return [];
        };

        $container['security.authorization'] = function () use ($container) {
            return new AuthorizationStack($container['security.authorization.authorizations']);
        };
    }

    /**
     * @param Container $container
     */
    private function registerRoleHierarchyResolver(Container $container)
    {
        $container['security.authorization.rolehierarchy'] = function () use ($container) {
            return [];
        };

        $container['security.authorization.rolehierarchyresolver'] = function () use ($container) {
            return new RoleHierarchyResolver(
                $container['security.authorization.rolehierarchy'],
                $container['logger'] ?? null
            );
        };
    }
}
