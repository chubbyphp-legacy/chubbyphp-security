<?php

namespace Chubbyphp\Tests\Security\Authentication\Exception;

use Chubbyphp\Security\Authentication\Exception\UserNotFoundException;

/**
 * @covers Chubbyphp\Security\Authentication\Exception\UserNotFoundException
 */
final class UserNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = UserNotFoundException::create(['username' => 'username']);

        self::assertSame('User not found with criteria username: username', $exception->getMessage());
    }
}
