<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\Security\UserInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AuthenticationStack implements AuthenticationInterface
{
    /**
     * @var AuthenticationInterface[]
     */
    private $authentications = [];

    /**
     * @param AuthenticationInterface[] $authentications
     */
    public function __construct(array $authentications)
    {
        foreach ($authentications as $authentication) {
            $this->addAuthentication($authentication);
        }
    }

    /**
     * @param AuthenticationInterface $authentication
     */
    private function addAuthentication(AuthenticationInterface $authentication)
    {
        $this->authentications[] = $authentication;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isAuthenticated(Request $request): bool
    {
        foreach ($this->authentications as $authentication) {
            if ($authentication->isAuthenticated($request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return UserInterface|null
     */
    public function getAuthenticatedUser(Request $request)
    {
        foreach ($this->authentications as $authentication) {
            if (null !== $user = $authentication->getAuthenticatedUser($request)) {
                return $user;
            }
        }

        return null;
    }
}
