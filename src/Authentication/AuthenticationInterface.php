<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\Security\UserInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

interface AuthenticationInterface
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isAuthenticated(Request $request): bool;

    /**
     * @param Request $request
     *
     * @return UserInterface|null
     */
    public function getAuthenticatedUser(Request $request);
}
