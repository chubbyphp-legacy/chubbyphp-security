<?php

namespace Chubbyphp\Tests\Security\Authorization;

use Chubbyphp\Security\Authorization\RoleAuthorization;
use Chubbyphp\Security\UserInterface;

/**
 * @covers Chubbyphp\Security\Authorization\RoleAuthorization
 */
final class RoleAuthorizationTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutHierarchy()
    {
        $authorization = new RoleAuthorization();

        self::assertFalse($authorization->isGranted($this->getUser(['USER_VIEW']), ['USER_EDIT', 'USER_VIEW']));
        self::assertTrue($authorization->isGranted($this->getUser(['USER_EDIT', 'USER_VIEW']), ['USER_VIEW']));
    }

    public function testWithHierarchy()
    {
        $authorization = new RoleAuthorization([
            'ADMIN' => ['MANAGE_USER'],
            'MANAGE_USER' => ['USER_CREATE', 'USER_EDIT', 'USER_VIEW', 'USER_DELETE']
        ]);

        for ($i = 0; $i < 10000; $i++) {
            self::assertTrue($authorization->isGranted($this->getUser(['ADMIN']), ['USER_EDIT', 'USER_VIEW']));
        }
    }

    public function testWithHierarchyInception()
    {
        $authorization = new RoleAuthorization([
            'SUPER_ADMIN' => ['ADMIN'],
            'ADMIN' => ['SUPER_ADMIN']
        ]);

        for ($i = 0; $i < 10000; $i++) {
            self::assertTrue($authorization->isGranted($this->getUser(['ADMIN']), ['SUPER_ADMIN']));
        }
    }

    /**
     * @param array $roles
     * @return UserInterface
     */
    private function getUser(array $roles): UserInterface
    {
        /** @var UserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMockBuilder(UserInterface::class)->setMethods(['getRoles'])->getMockForAbstractClass();

        $user->expects(self::any())->method('getRoles')->willReturn($roles);

        return $user;
    }
}
