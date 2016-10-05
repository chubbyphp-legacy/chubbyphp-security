<?php

namespace Chubbyphp\Security;

use Chubbyphp\Model\ModelInterface;

interface UserInterface extends ModelInterface
{
    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return string
     */
    public function getPassword(): string;
}
