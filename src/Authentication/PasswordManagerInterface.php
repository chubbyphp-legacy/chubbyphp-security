<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\Security\Authentication\Exception\EmptyPasswordException;

interface PasswordManagerInterface
{
    /**
     * @param string $password
     *
     * @return string
     *
     * @throws EmptyPasswordException
     */
    public function hash(string $password): string;

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function verify(string $password, string $hash): bool;
}
