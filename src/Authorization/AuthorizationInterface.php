<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authorization;

use Chubbyphp\Security\UserInterface;

interface AuthorizationInterface
{
    /**
     * @param UserInterface                  $user
     * @param mixed                          $attributes
     * @param OwnedByUserModelInterface|null $model
     *
     * @return bool
     */
    public function isGranted(UserInterface $user, $attributes, OwnedByUserModelInterface $model = null): bool;
}
