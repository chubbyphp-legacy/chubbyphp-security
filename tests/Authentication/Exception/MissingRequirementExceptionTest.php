<?php

namespace Chubbyphp\Tests\Security\Authentication\Exception;

use Chubbyphp\Security\Authentication\Exception\MissingRequirementException;

/**
 * @covers Chubbyphp\Security\Authentication\Exception\MissingRequirementException
 */
final class MissingRequirementExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = MissingRequirementException::create(['username', 'password']);

        self::assertSame('Missing required criteria username, password', $exception->getMessage());
    }
}
