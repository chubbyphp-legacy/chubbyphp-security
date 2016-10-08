<?php

namespace Chubbyphp\Security\Authentication\Exception;

final class InvalidPasswordException extends \RuntimeException implements AuthenticationExceptionInterface
{
    const MESSAGE = 'Invalid password';

    /**
     * @return InvalidPasswordException
     */
    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
