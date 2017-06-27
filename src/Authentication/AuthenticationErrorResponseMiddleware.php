<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class AuthenticationErrorResponseMiddleware
{
    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var AuthenticationErrorHandlerInterface
     */
    private $errorResponseHandler;

    /**
     * @param AuthenticationInterface $authentication
     * @param AuthenticationErrorHandlerInterface $errorResponseHandler
     */
    public function __construct(
        AuthenticationInterface $authentication,
        AuthenticationErrorHandlerInterface $errorResponseHandler
    ) {
        $this->authentication = $authentication;
        $this->errorResponseHandler = $errorResponseHandler;
    }

    /**
     * @param Request       $request
     * @param Response      $response
     * @param callable|null $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        if (!$this->authentication->isAuthenticated($request)) {
            return $this->errorResponseHandler->errorResponse($request, $response, 401);
        }

        if (null !== $next) {
            $response = $next($request, $response);
        }

        return $response;
    }
}
