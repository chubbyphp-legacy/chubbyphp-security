<?php

namespace Chubbyphp\Security\Authorization;

use Chubbyphp\Model\ModelInterface;

interface UserRolesInterface extends ModelInterface
{
    /**
     * @return string[]|array
     */
    public function getRoles(): array;
}
