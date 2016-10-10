<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\Security\Authentication\Exception\EmptyPasswordException;

final class PasswordManager implements PasswordManagerInterface
{
    /**
     * @param string $password
     *
     * @return string
     *
     * @throws EmptyPasswordException
     */
    public function hash(string $password): string
    {
        if ('' === $password) {
            throw EmptyPasswordException::create();
        }

        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
