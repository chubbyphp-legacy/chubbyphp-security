<?php

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\ErrorHandler\HttpException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class AuthenticationMiddleware
{
    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @param AuthenticationInterface $authentication
     */
    public function __construct(AuthenticationInterface $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @param Request       $request
     * @param Response      $response
     * @param callable|null $next
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        if (!$this->authentication->isAuthenticated($request)) {
            throw HttpException::create($request, $response, 401);
        }

        if (null !== $next) {
            $response = $next($request, $response);
        }

        return $response;
    }
}
