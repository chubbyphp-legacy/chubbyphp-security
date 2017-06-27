<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\ErrorHandler\HttpException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @deprecated use AuthenticationErrorResponseMiddleware
 */
final class AuthenticationMiddleware
{
    /**
     * @var AuthenticationErrorHandlerInterface
     */
    private $middleware;

    /**
     * @param AuthenticationInterface $authentication
     */
    public function __construct(AuthenticationInterface $authentication)
    {
        $this->middleware = new AuthenticationErrorResponseMiddleware(
            $authentication,
            new class() implements AuthenticationErrorHandlerInterface {
                public function errorResponse(Request $request, Response $response, int $code): Response
                {
                    throw HttpException::create($request, $response, $code);
                }
            }
        );
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
        return $this->middleware->__invoke($request, $response, $next);
    }
}
