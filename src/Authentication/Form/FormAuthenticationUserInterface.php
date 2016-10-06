<?php

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\Model\ModelInterface;

interface FormAuthenticationUserInterface extends ModelInterface
{
    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @return string
     */
    public function getPassword(): string;
}
