<?php

namespace Chubbyphp\Security\Authentication\Exception;

final class MissingRequirementException extends \RuntimeException implements AuthenticationExceptionInterface
{
    /**
     * @param array $fields
     *
     * @return MissingRequirementException
     */
    public static function create(array $fields): self
    {
        return new self(sprintf('Missing required criteria %s', implode(', ', $fields)));
    }
}
