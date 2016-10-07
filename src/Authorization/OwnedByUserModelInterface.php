<?php

namespace Chubbyphp\Security\Authorization;

interface OwnedByUserModelInterface
{
    /**
     * @return string
     */
    public function getOwnedByUserId(): string;
}
