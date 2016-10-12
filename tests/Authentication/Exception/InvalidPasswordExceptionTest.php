<?php

namespace Chubbyphp\Tests\Security\Authentication\Exception;

use Chubbyphp\Security\Authentication\Exception\InvalidPasswordException;

/**
 * @covers Chubbyphp\Security\Authentication\Exception\InvalidPasswordException
 */
final class InvalidPasswordExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = InvalidPasswordException::create(['username' => 'username']);

        self::assertSame('invalid password for user with criteria username: username', $exception->getMessage());
    }
}
