<?php

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
        $container['security.authorization.key'] = '';

        $container['security.authorization.roleauthorization'] = function () {
            return new RoleAuthorization();
        };

        $container['security.authorization'] = function () use ($container) {
            return $container[$container['security.authorization.key']];
        };
    }
}
