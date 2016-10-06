<?php

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authentication\Exception\AbstractLoginException;
use Psr\Http\Message\ServerRequestInterface as Request;

interface AuthenticationInterface
{
    const USER_KEY = 'user';

    /**
     * @param Request $request
     *
     * @throws AbstractLoginException
     */
    public function login(Request $request);

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isAuthenticated(Request $request): bool;

    /**
     * @param Request $request
     *
     * @return ModelInterface|null
     */
    public function getAuthenticatedUser(Request $request);
}
