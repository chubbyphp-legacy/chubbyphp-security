<?php

namespace Chubbyphp\Tests\Security\Authorization;

use Chubbyphp\Security\Authorization\OwnedByUserModelInterface;
use Chubbyphp\Security\Authorization\RoleAuthorization;
use Chubbyphp\Security\Authorization\RoleHierarchyResolverInterface;
use Chubbyphp\Security\UserInterface;

/**
 * @covers Chubbyphp\Security\Authorization\RoleAuthorization
 */
final class RoleAuthorizationTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutHierarchy()
    {
        $authorization = new RoleAuthorization($this->getRoleHierarchyResolver());

        self::assertFalse($authorization->isGranted($this->getUser('id1', ['USER_VIEW']), ['USER_EDIT', 'USER_VIEW']));
        self::assertTrue($authorization->isGranted($this->getUser('id1', ['USER_EDIT', 'USER_VIEW']), ['USER_VIEW']));
    }

    public function testWithHierarchy()
    {
        $authorization = new RoleAuthorization(
            $this->getRoleHierarchyResolver(['USER_EDIT', 'USER_VIEW'])
        );

        self::assertTrue($authorization->isGranted($this->getUser('id1', ['ADMIN']), ['USER_EDIT', 'USER_VIEW']));
    }

    public function testWithoutHierarchyAndModel()
    {
        $authorization = new RoleAuthorization($this->getRoleHierarchyResolver());

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
     * @param array $resolvedRoles
     *
     * @return RoleHierarchyResolverInterface
     */
    private function getRoleHierarchyResolver(array $resolvedRoles = []): RoleHierarchyResolverInterface
    {
        /** @var RoleHierarchyResolverInterface|\PHPUnit_Framework_MockObject_MockObject $resolver */
        $resolver = $this
            ->getMockBuilder(RoleHierarchyResolverInterface::class)
            ->setMethods(['resolve'])
            ->getMockForAbstractClass()
        ;

        $resolver
            ->expects(self::any())
            ->method('resolve')
            ->willReturnCallback(function (array $roles) use ($resolvedRoles) {
                $allRoles = array_merge($roles, $resolvedRoles);
                sort($allRoles);

                return $allRoles;
            })
        ;

        return $resolver;
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
        $user = $this
            ->getMockBuilder(UserInterface::class)
            ->setMethods(['getId', 'getRoles'])
            ->getMockForAbstractClass()
        ;

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
            ->setMethods(['getOwnedByUserId'])
            ->getMockForAbstractClass()
        ;

        $model->expects(self::any())->method('getOwnedByUserId')->willReturn($ownedByUserId);

        return $model;
    }
}
