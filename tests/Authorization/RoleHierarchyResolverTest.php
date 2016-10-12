<?php

namespace Chubbyphp\Tests\Security\Authorization;

use Chubbyphp\Security\Authorization\RoleHierarchyResolver;
use Chubbyphp\Tests\Security\LoggerTestTrait;

/**
 * @covers Chubbyphp\Security\Authorization\RoleHierarchyResolver
 */
final class RoleHierarchyResolverTest extends \PHPUnit_Framework_TestCase
{
    use LoggerTestTrait;

    public function testWithoutHierarchy()
    {
        $resolver = new RoleHierarchyResolver();

        self::assertSame(['USER_EDIT', 'USER_VIEW'], $resolver->resolve(['USER_EDIT', 'USER_VIEW']));
        self::assertSame(['USER_EDIT', 'USER_VIEW'], $resolver->resolve(['USER_VIEW', 'USER_EDIT']));
    }

    public function testWithHierarchy()
    {
        $resolver = new RoleHierarchyResolver([
            'ADMIN' => ['USER_MANAGEMENT'],
            'USER_MANAGEMENT' => ['USER_CREATE', 'USER_EDIT', 'USER_VIEW', 'USER_DELETE'],
        ]);

        self::assertSame([
            'ADMIN',
            'USER_CREATE',
            'USER_DELETE',
            'USER_EDIT',
            'USER_MANAGEMENT',
            'USER_VIEW',
        ], $resolver->resolve(['ADMIN']));

        self::assertSame([
            'USER_CREATE',
            'USER_DELETE',
            'USER_EDIT',
            'USER_MANAGEMENT',
            'USER_VIEW',
        ], $resolver->resolve(['USER_MANAGEMENT']));
    }

    public function testWithHierarchyInception()
    {
        $resolver = new RoleHierarchyResolver([
            'SUPER_ADMIN' => ['ADMIN'],
            'ADMIN' => ['SUPER_ADMIN'],
        ]);

        $resolver->resolve(['ADMIN']);
    }
}
