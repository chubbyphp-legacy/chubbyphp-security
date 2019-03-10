<?php

declare(strict_types=1);

namespace Chubbyphp\Security;

interface UserInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @return string[]|array
     */
    public function getRoles(): array;
}
