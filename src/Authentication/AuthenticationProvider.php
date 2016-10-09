<?php

namespace Chubbyphp\Security\Authentication;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class AuthenticationProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['security.authentication.passwordmanager'] = function () {
            return new PasswordManager();
        };

        $this->registerAuthentication($container);
        $this->registerMiddleware($container);
    }

    /**
     * @param Container $container
     */
    private function registerAuthentication(Container $container)
    {
        $container['security.authentication.key'] = '';
        $container['security.userrepository.key'] = '';

        $container['security.authentication.formauthentication'] = function ($container) {
            return new FormAuthentication(
                $container['security.authentication.passwordmanager'],
                $container['session'],
                $container[$container['security.userrepository.key']]
            );
        };

        $container['security.authentication'] = function () use ($container) {
            return $container[$container['security.authentication.key']];
        };
    }

    /**
     * @param Container $container
     */
    private function registerMiddleware(Container $container)
    {
        $container['security.authentication.middleware'] = function () use ($container) {
            return new AuthenticationMiddleware($container['security.authentication']);
        };
    }
}
