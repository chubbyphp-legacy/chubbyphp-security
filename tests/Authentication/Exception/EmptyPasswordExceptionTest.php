<?php

namespace Chubbyphp\Tests\Security\Authentication\Exception;

use Chubbyphp\Security\Authentication\Exception\EmptyPasswordException;

/**
 * @covers \Chubbyphp\Security\Authentication\Exception\EmptyPasswordException
 */
final class EmptyPasswordExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $exception = EmptyPasswordException::create();

        self::assertSame(EmptyPasswordException::MESSAGE, $exception->getMessage());
    }
}
