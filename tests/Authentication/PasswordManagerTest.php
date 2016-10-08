<?php

namespace Chubbyphp\Tests\Security\Authentication;

use Chubbyphp\Security\Authentication\Exception\EmptyPasswordException;
use Chubbyphp\Security\Authentication\PasswordManager;

/**
 * @covers Chubbyphp\Security\Authentication\PasswordManager
 */
final class PasswordManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testHashEmpty()
    {
        self::expectException(EmptyPasswordException::class);
        self::expectExceptionMessage(EmptyPasswordException::MESSAGE);

        $manager = new PasswordManager();
        $manager->hash('');
    }

    public function testHash()
    {
        $manager = new PasswordManager();

        self::assertStringStartsWith('$2', $manager->hash('password'));
    }

    public function testVerfifyValid()
    {
        $manager = new PasswordManager();

        self::assertTrue($manager->verify('password', '$2y$10$yj82A139KlL7H7lf/Mh1nuOEPcb0JmiHe8cAFRCg3fVpo9NBYhCfi'));
    }

    public function testVerfifyInvalid()
    {
        $manager = new PasswordManager();

        self::assertFalse($manager->verify('password', '$2y$10$yj82A139KlL7H7lf/Mh1nuOEPcb0JmiHe8cAFRCg3fVpo9NBYhCaa'));
    }
}
