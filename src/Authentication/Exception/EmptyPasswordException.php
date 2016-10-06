<?php

namespace Chubbyphp\Security\Authentication\Exception;

final class EmptyPasswordException extends \Exception
{
    /**
     * @return EmptyPasswordException
     */
    public static function create(): self
    {
        return new self('Empty password');
    }
}