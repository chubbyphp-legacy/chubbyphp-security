<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface AuthenticationErrorHandlerInterface
{
    /**
     * @param Request  $request
     * @param Response $response
     * @param int      $code
     *
     * @return Response
     */
    public function errorResponse(Request $request, Response $response, int $code): Response;
}
