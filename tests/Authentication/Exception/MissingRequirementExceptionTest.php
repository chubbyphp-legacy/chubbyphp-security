<?php

namespace Chubbyphp\Tests\Security\Authentication\Exception;

use Chubbyphp\Security\Authentication\Exception\MissingRequirementException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Security\Authentication\Exception\MissingRequirementException
 */
final class MissingRequirementExceptionTest extends TestCase
{
    public function testException()
    {
        $exception = MissingRequirementException::create(['username', 'password']);

        self::assertSame('missing required fields username, password', $exception->getMessage());
    }
}
