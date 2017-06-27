<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\ErrorHandler\HttpException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
        $container['security.authentication.errorResponseHandler'] = new class() implements AuthenticationErrorHandlerInterface {
            public function errorResponse(
                Request $request,
                Response $response,
                int $code,
                string $reasonPhrase = null
            ): Response {
                throw HttpException::create($request, $response, $code, $reasonPhrase);
            }
        };

        $container['security.authentication.middleware'] = function () use ($container) {
            return new AuthenticationErrorResponseMiddleware(
                $container['security.authentication'],
                $container['security.authentication.errorResponseHandler']
            );
        };
    }
}
