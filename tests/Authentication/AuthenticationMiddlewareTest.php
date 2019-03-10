<?php

namespace Chubbyphp\Tests\Security\Authentication;

use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authentication\AuthenticationMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @covers \Chubbyphp\Security\Authentication\AuthenticationMiddleware
 */
final class AuthenticationMiddlewareTest extends TestCase
{
    public function testWithoutAuthenticationHttpException()
    {
        self::expectException(HttpException::class);
        self::expectExceptionCode(401);

        $middleware = new AuthenticationMiddleware($this->getAuthentication(false));
        $middleware($this->getRequest(), $this->getResponse());
    }

    public function testWithAuthentication()
    {
        $middleware = new AuthenticationMiddleware($this->getAuthentication(true));
        $middleware($this->getRequest(), $this->getResponse());
    }

    public function testWithAuthenticationAndNext()
    {
        $middleware = new AuthenticationMiddleware($this->getAuthentication(true));
        $middleware(
            $this->getRequest(),
            $this->getResponse(),
            function (Request $request, Response $response, callable $next = null) {
                return $response;
            }
        );
    }

    /**
     * @param bool $isAuthenticated
     *
     * @return AuthenticationInterface
     */
    private function getAuthentication(bool $isAuthenticated): AuthenticationInterface
    {
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
            ->setMethods(['getMethod'])
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
            ->setMethods(['withStatus'])
            ->getMockForAbstractClass()
        ;
    }
}
