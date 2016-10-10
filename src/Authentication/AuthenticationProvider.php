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
        $container['security.authentication.authentications'] = function () {
            return [];
        };

        $container['security.authentication'] = function () use ($container) {
            return new AuthenticationStack($container['security.authentication.authentications']);
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
