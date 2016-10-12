<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication\Exception;

final class UserNotFoundException extends \RuntimeException implements AuthenticationExceptionInterface
{
    /**
     * @param array $criteria
     *
     * @return UserNotFoundException
     */
    public static function create(array $criteria): self
    {
        return new self(sprintf('user not found with criteria %s', self::getCriteriaAsSting($criteria)));
    }

    /**
     * @param array $criteria
     *
     * @return string
     */
    private static function getCriteriaAsSting(array $criteria): string
    {
        $criteriaString = '';
        foreach ($criteria as $key => $value) {
            $criteriaString .= $key.': '.$value.', ';
        }

        return substr($criteriaString, 0, -2);
    }
}
