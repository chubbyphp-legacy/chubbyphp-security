<?php

declare(strict_types=1);

namespace Chubbyphp\Security;

interface UserRepositoryInterface
{
    /**
     * @param string $id
     *
     * @return UserInterface|null
     */
    public function find(string $id);

    /**
     * @param string $username
     *
     * @return UserInterface|null
     */
    public function findByUsername(string $username);
}
