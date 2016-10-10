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
        $container['security.authorization.authorizations'] = function () use ($container) {
            return [];
        };

        $container['security.authorization'] = function () use ($container) {
            return new AuthorizationStack($container['security.authorization.authorizations']);
        };
    }
}
