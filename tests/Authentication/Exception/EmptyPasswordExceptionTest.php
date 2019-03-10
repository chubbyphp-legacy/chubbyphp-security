<?php

namespace Chubbyphp\Tests\Security\Authentication\Exception;

use Chubbyphp\Security\Authentication\Exception\EmptyPasswordException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Security\Authentication\Exception\EmptyPasswordException
 */
final class EmptyPasswordExceptionTest extends TestCase
{
    public function testException()
    {
        $exception = EmptyPasswordException::create();

        self::assertSame(EmptyPasswordException::MESSAGE, $exception->getMessage());
    }
}
