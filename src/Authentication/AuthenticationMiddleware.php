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
    private $auth;

    /**
     * @param AuthenticationInterface $auth
     */
    public function __construct(AuthenticationInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->auth->isAuthenticated($request)) {
            throw HttpException::create($request, $response, 401);
        }

        $response = $next($request, $response);

        return $response;
    }
}
