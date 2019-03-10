<?php

namespace Chubbyphp\Tests\Security\Authorization;

use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Chubbyphp\Security\Authorization\AuthorizationStack;
use Chubbyphp\Security\Authorization\OwnedByUserModelInterface;
use Chubbyphp\Security\UserInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Security\Authorization\AuthorizationStack
 */
final class AuthorizationStackTest extends TestCase
{
    public function testIsAuthorizedWithoutGrant()
    {
        $authentication = new AuthorizationStack([
            $this->getAuthorization(),
            $this->getAuthorization(),
        ]);

        self::assertFalse($authentication->isGranted($this->getUser('id1', 'username'), ['USER_EDIT']));
    }

    public function testIsAuthorizedWithGrant()
    {
        $authentication = new AuthorizationStack([
            $this->getAuthorization(),
            $this->getAuthorization(true),
        ]);

        self::assertTrue($authentication->isGranted($this->getUser('id1', 'username'), ['USER_EDIT']));
    }

    /**
     * @param bool $isGranted
     *
     * @return AuthorizationInterface
     */
    private function getAuthorization(bool $isGranted = false): AuthorizationInterface
    {
        /** @var AuthorizationInterface|\PHPUnit_Framework_MockObject_MockObject $authentication */
        $authentication = $this
            ->getMockBuilder(AuthorizationInterface::class)
            ->setMethods(['isGranted'])
            ->getMockForAbstractClass()
        ;

        $authentication
            ->expects(self::any())
            ->method('isGranted')
            ->willReturnCallback(
                function (UserInterface $user, $attributes, OwnedByUserModelInterface $model = null) use ($isGranted) {
                    return $isGranted;
                }
            )
        ;

        return $authentication;
    }

    /**
     * @param string $id
     * @param string $username
     *
     * @return UserInterface
     */
    private function getUser(string $id, string $username): UserInterface
    {
        /** @var UserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this
            ->getMockBuilder(UserInterface::class)
            ->setMethods(['getId', 'getUsername', 'getPassword'])
            ->getMockForAbstractClass()
        ;

        $user->expects(self::any())->method('getId')->willReturn($id);
        $user->expects(self::any())->method('getUsername')->willReturn($username);

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
