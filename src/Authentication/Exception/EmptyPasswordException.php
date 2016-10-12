<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication\Exception;

final class EmptyPasswordException extends \RuntimeException implements AuthenticationExceptionInterface
{
    const MESSAGE = 'empty  password';

    /**
     * @return EmptyPasswordException
     */
    public static function create(): self
    {
        return new self(self::MESSAGE);
    }
}
