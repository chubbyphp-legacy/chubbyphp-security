<?php

namespace Chubbyphp\Tests\Security\Authorization;

use Chubbyphp\Security\Authorization\AuthorizationProvider;
use Chubbyphp\Security\Authorization\AuthorizationStack;
use Chubbyphp\Security\Authorization\RoleHierarchyResolver;
use Pimple\Container;

/**
 * @covers \Chubbyphp\Security\Authorization\AuthorizationProvider
 */
final class AuthorizationProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $container = new Container();
        $container->register(new AuthorizationProvider());

        self::assertArrayHasKey('security.authorization.rolehierarchy', $container);
        self::assertArrayHasKey('security.authorization.rolehierarchyresolver', $container);

        self::assertSame([], $container['security.authorization.rolehierarchy']);
        self::assertInstanceOf(
            RoleHierarchyResolver::class,
            $container['security.authorization.rolehierarchyresolver']
        );

        self::assertArrayHasKey('security.authorization.authorizations', $container);
        self::assertArrayHasKey('security.authorization', $container);

        self::assertSame([], $container['security.authorization.authorizations']);
        self::assertInstanceOf(AuthorizationStack::class, $container['security.authorization']);
    }
}
