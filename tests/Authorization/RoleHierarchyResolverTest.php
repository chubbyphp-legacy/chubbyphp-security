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
        $logger = $this->getLogger();

        $resolver = new RoleHierarchyResolver([], $logger);

        self::assertSame(['USER_EDIT', 'USER_VIEW'], $resolver->resolve(['USER_EDIT', 'USER_VIEW']));
        self::assertSame(['USER_EDIT', 'USER_VIEW'], $resolver->resolve(['USER_VIEW', 'USER_EDIT']));

        self::assertCount(2, $logger->__logs);
        self::assertSame('info', $logger->__logs[0]['level']);
        self::assertSame(
            'security.authorization.rolehierarchyresolver: resolved roles {resolvedRoles} by given roles {roles}',
            $logger->__logs[0]['message']
        );
        self::assertSame(
            ['resolvedRoles' => 'USER_EDIT, USER_VIEW', 'roles' => 'USER_EDIT, USER_VIEW'],
            $logger->__logs[0]['context']
        );
        self::assertSame('info', $logger->__logs[1]['level']);
        self::assertSame(
            'security.authorization.rolehierarchyresolver: resolved roles {resolvedRoles} by given roles {roles}',
            $logger->__logs[1]['message']
        );
        self::assertSame(
            ['resolvedRoles' => 'USER_EDIT, USER_VIEW', 'roles' => 'USER_VIEW, USER_EDIT'],
            $logger->__logs[1]['context']
        );
    }

    public function testWithHierarchy()
    {
        $logger = $this->getLogger();

        $resolver = new RoleHierarchyResolver([
            'ADMIN' => ['USER_MANAGEMENT'],
            'USER_MANAGEMENT' => ['USER_CREATE', 'USER_EDIT', 'USER_VIEW', 'USER_DELETE'],
        ], $logger);

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

        self::assertCount(2, $logger->__logs);
        self::assertSame('info', $logger->__logs[0]['level']);
        self::assertSame(
            'security.authorization.rolehierarchyresolver: resolved roles {resolvedRoles} by given roles {roles}',
            $logger->__logs[0]['message']
        );
        self::assertSame(
            [
                'resolvedRoles' => 'ADMIN, USER_CREATE, USER_DELETE, USER_EDIT, USER_MANAGEMENT, USER_VIEW',
                'roles' => 'ADMIN',
            ],
            $logger->__logs[0]['context']
        );
        self::assertSame('info', $logger->__logs[1]['level']);
        self::assertSame(
            'security.authorization.rolehierarchyresolver: resolved roles {resolvedRoles} by given roles {roles}',
            $logger->__logs[1]['message']
        );
        self::assertSame(
            [
                'resolvedRoles' => 'USER_CREATE, USER_DELETE, USER_EDIT, USER_MANAGEMENT, USER_VIEW',
                'roles' => 'USER_MANAGEMENT',
            ],
            $logger->__logs[1]['context']
        );
    }

    public function testWithHierarchyInception()
    {
        $logger = $this->getLogger();

        $resolver = new RoleHierarchyResolver([
            'SUPER_ADMIN' => ['ADMIN'],
            'ADMIN' => ['SUPER_ADMIN'],
        ], $logger);

        $resolver->resolve(['ADMIN']);

        self::assertCount(1, $logger->__logs);
        self::assertSame('info', $logger->__logs[0]['level']);
        self::assertSame(
            'security.authorization.rolehierarchyresolver: resolved roles {resolvedRoles} by given roles {roles}',
            $logger->__logs[0]['message']
        );
        self::assertSame(
            [
                'resolvedRoles' => 'ADMIN, SUPER_ADMIN',
                'roles' => 'ADMIN',
            ],
            $logger->__logs[0]['context']
        );
    }
}
