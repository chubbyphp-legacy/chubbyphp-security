<?php

namespace Chubbyphp\Security\Authentication\Exception;

final class EmptyPasswordException extends \RuntimeException implements AuthenticationExceptionInterface
{
    const MESSAGE = 'Empty password';

    /**
     * @return EmptyPasswordException
     */
    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
