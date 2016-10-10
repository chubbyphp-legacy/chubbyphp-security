<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authorization;

use Chubbyphp\Security\UserInterface;

final class AuthorizationStack implements AuthorizationInterface
{
    /**
     * @var AuthorizationInterface[]
     */
    private $authorizations = [];

    /**
     * @param AuthorizationInterface[] $authorizations
     */
    public function __construct(array $authorizations)
    {
        foreach ($authorizations as $authorization) {
            $this->addAuthorization($authorization);
        }
    }

    /**
     * @param AuthorizationInterface $authorization
     */
    private function addAuthorization(AuthorizationInterface $authorization)
    {
        $this->authorizations[] = $authorization;
    }

    /**
     * @param UserInterface                  $user
     * @param mixed                          $attributes
     * @param OwnedByUserModelInterface|null $model
     *
     * @return bool
     */
    public function isGranted(UserInterface $user, $attributes, OwnedByUserModelInterface $model = null): bool
    {
        foreach ($this->authorizations as $authorization) {
            if ($authorization->isGranted($user, $attributes, $model)) {
                return true;
            }
        }

        return false;
    }
}
