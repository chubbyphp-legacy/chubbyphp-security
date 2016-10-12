<?php

declare(strict_types=1);

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
        return new self(sprintf('missing required fields %s', implode(', ', $fields)));
    }
}
