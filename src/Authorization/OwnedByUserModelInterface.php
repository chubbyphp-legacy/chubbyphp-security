<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authorization;

interface OwnedByUserModelInterface
{
    /**
     * @return string
     */
    public function getOwnedByUserId(): string;
}
