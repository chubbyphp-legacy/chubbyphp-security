<?php

namespace Chubbyphp\Security\Authorization;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\UserInterface;

interface AuthorizationInterface
{
    /**
     * @param UserInterface       $user
     * @param mixed               $attributes
     * @param ModelInterface|null $model
     *
     * @return bool
     */
    public function isGranted(UserInterface $user, $attributes, ModelInterface $model = null): bool;
}
