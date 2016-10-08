<?php

namespace Chubbyphp\Security;

use Chubbyphp\Model\ModelInterface;

interface UserInterface extends ModelInterface
{
    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @return string[]|array
     */
    public function getRoles(): array;
}