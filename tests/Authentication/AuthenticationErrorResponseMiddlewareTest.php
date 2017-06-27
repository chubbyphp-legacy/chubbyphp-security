<?php

namespace Chubbyphp\Tests\Security\Authentication;

use Chubbyphp\Security\Authentication\AuthenticationErrorHandlerInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authentication\AuthenticationErrorResponseMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @covers \Chubbyphp\Security\Authentication\AuthenticationErrorResponseMiddleware
 */
final class AuthenticationErrorResponseMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutAuthenticationHttpException()
    {
        $middleware = new AuthenticationErrorResponseMiddleware(
            $this->getAuthentication(false),
            $this->getErrorHandler()
        );

        $request = $this->getRequest();
        $response = $this->getResponse();

        self::assertSame($response, $middleware($request, $response));
        self::assertSame(401, $response->getStatusCode());
    }

    public function testWithAuthentication()
    {
        $middleware = new AuthenticationErrorResponseMiddleware(
            $this->getAuthentication(true),
            $this->getErrorHandler()
        );

        $request = $this->getRequest();
        $response = $this->getResponse();

        self::assertSame($response, $middleware($request, $response));
    }

    public function testWithAuthenticationAndNext()
    {
        $middleware = new AuthenticationErrorResponseMiddleware(
            $this->getAuthentication(true),
            $this->getErrorHandler()
        );

        $request = $this->getRequest();
        $response = $this->getResponse();

        self::assertSame($response,
            $middleware(
                $request,
                $response,
                function (Request $request, Response $response, callable $next = null) {
                    return $response;
                }
            )
        );
    }

    /**
     * @param bool $isAuthenticated
     *
     * @return AuthenticationInterface
     */
    private function getAuthentication(bool $isAuthenticated): AuthenticationInterface
    {
        /** @var AuthenticationInterface|\PHPUnit_Framework_MockObject_MockObject $authentication */
        $authentication = $this
            ->getMockBuilder(AuthenticationInterface::class)
            ->setMethods(['isAuthenticated'])
            ->getMockForAbstractClass()
        ;

        $authentication->expects(self::any())->method('isAuthenticated')->willReturn($isAuthenticated);

        return $authentication;
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
        $response = $this
            ->getMockBuilder(Response::class)
            ->setMethods(['withStatus', 'getStatusCode'])
            ->getMockForAbstractClass()
        ;

        $response->__data = [
            'code' => null
        ];

        $response
            ->expects(self::any())
            ->method('withStatus')
            ->willReturnCallback(
                function (int $code) use ($response) {
                    $response->__data['code'] = $code;

                    return $response;
                }
            )
        ;

        $response
            ->expects(self::any())
            ->method('getStatusCode')
            ->willReturnCallback(
                function () use ($response) {
                    return $response->__data['code'];
                }
            )
        ;

        return $response;
    }

    /**
     * @return AuthenticationErrorHandlerInterface
     */
    private function getErrorHandler(): AuthenticationErrorHandlerInterface
    {
        /** @var AuthenticationErrorHandlerInterface|\PHPUnit_Framework_MockObject_MockObject $errorHandler */
        $errorHandler = $this
            ->getMockBuilder(AuthenticationErrorHandlerInterface::class)
            ->setMethods(['errorResponse'])
            ->getMockForAbstractClass()
        ;

        $errorHandler
            ->expects(self::any())
            ->method('errorResponse')
            ->willReturnCallback(
                function (Request $request, Response $response, int $code) {
                    return $response->withStatus($code);
                }
            )
        ;

        return $errorHandler;
    }
}
