<?php

namespace Chubbyphp\Tests\Security\Authentication;

use Chubbyphp\Security\Authentication\AuthenticationMiddleware;
use Chubbyphp\Security\Authentication\AuthenticationProvider;
use Chubbyphp\Security\Authentication\AuthenticationStack;
use Chubbyphp\Security\Authentication\PasswordManager;
use Pimple\Container;

/**
 * @covers \Chubbyphp\Security\Authentication\AuthenticationProvider
 */
final class AuthenticationProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $container = new Container();
        $container->register(new AuthenticationProvider());

        self::assertArrayHasKey('security.authentication.passwordmanager', $container);
        self::assertInstanceOf(PasswordManager::class, $container['security.authentication.passwordmanager']);

        self::assertArrayHasKey('security.authentication.authentications', $container);
        self::assertArrayHasKey('security.authentication', $container);

        self::assertSame([], $container['security.authentication.authentications']);
        self::assertInstanceOf(AuthenticationStack::class, $container['security.authentication']);

        self::assertArrayHasKey('security.authentication.middleware', $container);
        self::assertInstanceOf(AuthenticationMiddleware::class, $container['security.authentication.middleware']);
    }
}
