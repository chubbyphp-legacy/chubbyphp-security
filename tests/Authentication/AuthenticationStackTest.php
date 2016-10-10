<?php

namespace Chubbyphp\Tests\Security\Authentication;

use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authentication\AuthenticationStack;
use Chubbyphp\Security\UserInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @covers Chubbyphp\Security\Authentication\AuthenticationStack
 */
final class AuthenticationStackTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAuthenticatedWithoutUser()
    {
        $authentication = new AuthenticationStack([
            $this->getAuthentication(),
            $this->getAuthentication(),
        ]);

        self::assertFalse($authentication->isAuthenticated($this->getRequest()));
    }

    public function testIsAuthenticatedWithUser()
    {
        $authentication = new AuthenticationStack([
            $this->getAuthentication(),
            $this->getAuthentication($this->getUser('id1', 'username')),
        ]);

        self::assertTrue($authentication->isAuthenticated($this->getRequest()));
    }

    public function testGetAuthenticatedUserWithoutUser()
    {
        $authentication = new AuthenticationStack([
            $this->getAuthentication(),
            $this->getAuthentication(),
        ]);

        self::assertNull($authentication->getAuthenticatedUser($this->getRequest()));
    }

    public function testGetAuthenticatedUserWithUser()
    {
        $authentication = new AuthenticationStack([
            $this->getAuthentication(),
            $this->getAuthentication($this->getUser('id1', 'username')),
        ]);

        $user = $authentication->getAuthenticatedUser($this->getRequest());

        self::assertInstanceOf(UserInterface::class, $user);

        self::assertSame('id1', $user->getId());
        self::assertSame('username', $user->getUsername());
    }

    /**
     * @param UserInterface|null $user
     *
     * @return AuthenticationInterface
     */
    private function getAuthentication(UserInterface $user = null): AuthenticationInterface
    {
        /** @var AuthenticationInterface|\PHPUnit_Framework_MockObject_MockObject $authentication */
        $authentication = $this
            ->getMockBuilder(AuthenticationInterface::class)
            ->setMethods(['isAuthenticated', 'getAuthenticatedUser'])
            ->getMockForAbstractClass()
        ;

        $authentication
            ->expects(self::any())
            ->method('isAuthenticated')
            ->willReturnCallback(function () use ($user) {
                return null !== $user;
            })
        ;

        $authentication
            ->expects(self::any())
            ->method('getAuthenticatedUser')
            ->willReturnCallback(function () use ($user) {
                return $user;
            })
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
     * @param array $body
     *
     * @return Request
     */
    private function getRequest(array $body = []): Request
    {
        $repository = $this
            ->getMockBuilder(Request::class)
            ->setMethods(['getParsedBody'])
            ->getMockForAbstractClass()
        ;

        $repository
            ->expects(self::any())
            ->method('getParsedBody')
            ->willReturnCallback(function () use ($body) {
                return $body;
            })
        ;

        return $repository;
    }
}
