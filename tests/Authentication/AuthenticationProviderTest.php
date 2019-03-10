<?php

namespace Chubbyphp\Tests\Security\Authentication;

use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\Security\Authentication\AuthenticationErrorHandlerInterface;
use Chubbyphp\Security\Authentication\AuthenticationErrorResponseMiddleware;
use Chubbyphp\Security\Authentication\AuthenticationProvider;
use Chubbyphp\Security\Authentication\AuthenticationStack;
use Chubbyphp\Security\Authentication\PasswordManager;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @covers \Chubbyphp\Security\Authentication\AuthenticationProvider
 */
final class AuthenticationProviderTest extends TestCase
{
    public function testRegister()
    {
        $container = new Container();
        $container->register(new AuthenticationProvider());

        self::assertArrayHasKey('security.authentication.passwordmanager', $container);
        self::assertInstanceOf(PasswordManager::class, $container['security.authentication.passwordmanager']);

        self::assertArrayHasKey('security.authentication.authentications', $container);
        self::assertArrayHasKey('security.authentication', $container);

        self::assertSame([], $container['security.authentication.authentications']);
        self::assertInstanceOf(AuthenticationStack::class, $container['security.authentication']);

        self::assertArrayHasKey('security.authentication.errorResponseHandler', $container);
        self::assertInstanceOf(AuthenticationErrorHandlerInterface::class, $container['security.authentication.errorResponseHandler']);

        self::assertArrayHasKey('security.authentication.middleware', $container);
        self::assertInstanceOf(AuthenticationErrorResponseMiddleware::class, $container['security.authentication.middleware']);

        $request = $this->getRequest();
        $response = $this->getResponse();

        try {
            $container['security.authentication.errorResponseHandler']->errorResponse($request, $response, 424, 'test');
        } catch (HttpException $e) {
            self::assertSame(424, $e->getCode());
            self::assertSame('test', $e->getMessage());

            return;
        }

        self::fail(sprintf('Expected %s', HttpException::class));
    }

    /**
     * @return Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequest(): Request
    {
        return $this
            ->getMockBuilder(Request::class)
            ->getMockForAbstractClass()
        ;
    }

    /**
     * @return Response|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponse(): Response
    {
        return $this
            ->getMockBuilder(Response::class)
            ->getMockForAbstractClass()
        ;
    }
}
