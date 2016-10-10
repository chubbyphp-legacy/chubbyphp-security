<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\Security\UserInterface;

interface UserPasswordInterface extends UserInterface
{
    /**
     * @return string
     */
    public function getPassword(): string;
}
