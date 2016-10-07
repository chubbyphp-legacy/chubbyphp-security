<?php

namespace Chubbyphp\Tests\Security\Authorization;

use Chubbyphp\Security\Authorization\OwnedByUserModelInterface;
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

        self::assertFalse($authorization->isGranted($this->getUser('id1', ['USER_VIEW']), ['USER_EDIT', 'USER_VIEW']));
        self::assertTrue($authorization->isGranted($this->getUser('id1', ['USER_EDIT', 'USER_VIEW']), ['USER_VIEW']));
    }

    public function testWithHierarchy()
    {
        $authorization = new RoleAuthorization([
            'ADMIN' => ['MANAGE_USER'],
            'MANAGE_USER' => ['USER_CREATE', 'USER_EDIT', 'USER_VIEW', 'USER_DELETE'],
        ]);

        self::assertTrue($authorization->isGranted($this->getUser('id1', ['ADMIN']), ['USER_EDIT', 'USER_VIEW']));
    }

    public function testWithHierarchyInception()
    {
        $authorization = new RoleAuthorization([
            'SUPER_ADMIN' => ['ADMIN'],
            'ADMIN' => ['SUPER_ADMIN'],
        ]);

        self::assertTrue($authorization->isGranted($this->getUser('id1', ['ADMIN']), ['SUPER_ADMIN']));
    }

    public function testWithoutHierarchyAndModel()
    {
        $authorization = new RoleAuthorization();

        self::assertFalse($authorization->isGranted(
            $this->getUser('id1', ['USER_VIEW']),
            ['USER_EDIT', 'USER_VIEW'],
            $this->getModel('id1')
        ));

        self::assertTrue($authorization->isGranted(
            $this->getUser('id1', ['USER_EDIT', 'USER_VIEW']),
            ['USER_VIEW'],
            $this->getModel('id1')
        ));

        self::assertFalse($authorization->isGranted(
            $this->getUser('id1', ['USER_VIEW']),
            ['USER_EDIT', 'USER_VIEW'],
            $this->getModel('id2')
        ));

        self::assertFalse($authorization->isGranted(
            $this->getUser('id1', ['USER_EDIT', 'USER_VIEW']),
            ['USER_VIEW'],
            $this->getModel('id2')
        ));
    }

    /**
     * @param string $id
     * @param array  $roles
     *
     * @return UserInterface
     */
    private function getUser(string $id, array $roles): UserInterface
    {
        /** @var UserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMockBuilder(UserInterface::class)->setMethods(['getRoles'])->getMockForAbstractClass();

        $user->expects(self::any())->method('getId')->willReturn($id);
        $user->expects(self::any())->method('getRoles')->willReturn($roles);

        return $user;
    }

    /**
     * @param string $ownedByUserId
     *
     * @return OwnedByUserModelInterface
     */
    private function getModel(string $ownedByUserId): OwnedByUserModelInterface
    {
        /** @var OwnedByUserModelInterface|\PHPUnit_Framework_MockObject_MockObject $model */
        $model = $this
            ->getMockBuilder(OwnedByUserModelInterface::class)
            ->setMethods(['getRoles'])
            ->getMockForAbstractClass()
        ;

        $model->expects(self::any())->method('getOwnedByUserId')->willReturn($ownedByUserId);

        return $model;
    }
}
