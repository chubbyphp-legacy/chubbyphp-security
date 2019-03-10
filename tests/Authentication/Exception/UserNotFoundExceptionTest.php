<?php

namespace Chubbyphp\Tests\Security\Authentication\Exception;

use Chubbyphp\Security\Authentication\Exception\UserNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Security\Authentication\Exception\UserNotFoundException
 */
final class UserNotFoundExceptionTest extends TestCase
{
    public function testException()
    {
        $exception = UserNotFoundException::create(['username' => 'username']);

        self::assertSame('user not found with criteria username: username', $exception->getMessage());
    }
}
