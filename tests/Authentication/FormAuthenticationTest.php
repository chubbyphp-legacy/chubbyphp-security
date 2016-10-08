<?php

namespace Chubbyphp\Tests\Security\Authentication;

use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Security\Authentication\Exception\InvalidPasswordException;
use Chubbyphp\Security\Authentication\Exception\MissingRequirementException;
use Chubbyphp\Security\Authentication\Exception\UserNotFoundException;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use Chubbyphp\Security\UserInterface;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @covers Chubbyphp\Security\Authentication\FormAuthentication
 */
final class FormAuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testLoginWithoutUsernameAndPasswordExpectException()
    {
        self::expectException(MissingRequirementException::class);
        self::expectExceptionMessage('Missing required criteria username, password');

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $this->getSession(),
            $this->getRepository()
        );

        $authentication->login($this->getRequest());
    }

    public function testLoginWithoutUsernameExpectException()
    {
        self::expectException(MissingRequirementException::class);
        self::expectExceptionMessage('Missing required criteria username');

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $this->getSession(),
            $this->getRepository()
        );

        $authentication->login($this->getRequest(['password' => 'password']));
    }

    public function testLoginWithoutUPasswordExpectException()
    {
        self::expectException(MissingRequirementException::class);
        self::expectExceptionMessage('Missing required criteria password');

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $this->getSession(),
            $this->getRepository()
        );

        $authentication->login($this->getRequest(['username' => 'username']));
    }

    public function testLoginUserNotFoundExpectException()
    {
        self::expectException(UserNotFoundException::class);
        self::expectExceptionMessage('User not found with criteria username: username');

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $this->getSession(),
            $this->getRepository()
        );

        $authentication->login($this->getRequest(['username' => 'username', 'password' => 'password']));
    }

    public function testLoginWithInvalidPasswordExpectException()
    {
        self::expectException(InvalidPasswordException::class);
        self::expectExceptionMessage(InvalidPasswordException::MESSAGE);

        $user = $this->getUser('id1', 'username', 'invalidpassword');

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $this->getSession(),
            $this->getRepository($user)
        );

        $authentication->login($this->getRequest(['username' => 'username', 'password' => 'password']));
    }

    public function testLogin()
    {
        $user = $this->getUser('id1', 'username', 'password');

        $session = $this->getSession();

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository($user)
        );

        $authentication->login($this->getRequest(['username' => 'username', 'password' => 'password']));

        self::assertArrayHasKey(FormAuthentication::USER_KEY, $session->__storage);
        self::assertSame('id1', $session->__storage[FormAuthentication::USER_KEY]);
    }

    public function testLogout()
    {
        $session = $this->getSession();
        $session->__storage[FormAuthentication::USER_KEY] = 'id1';

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository()
        );

        $authentication->logout($this->getRequest([]));

        self::assertArrayNotHasKey(FormAuthentication::USER_KEY, $session->__storage);
    }

    public function testGetAuthenticatedUserWithoutUserIdInSession()
    {
        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $this->getSession(),
            $this->getRepository()
        );

        self::assertNull($authentication->getAuthenticatedUser($this->getRequest()));
    }

    public function testGetAuthenticatedUserWithoutUserInReository()
    {
        $session = $this->getSession();
        $session->__storage[FormAuthentication::USER_KEY] = 'id1';

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository()
        );
        $authentication->getAuthenticatedUser($this->getRequest());
    }

    public function testGetAuthenticatedUser()
    {
        $user = $this->getUser('id1', 'username', '$2y$10$zXfRRDa2u9WxgB0noAnk1u281vVwNwjNcH5WCRdu8I70aBk23TS6G');

        $session = $this->getSession();
        $session->__storage[FormAuthentication::USER_KEY] = 'id1';

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository($user)
        );

        $user = $authentication->getAuthenticatedUser($this->getRequest());

        self::assertInstanceOf(UserInterface::class, $user);
    }

    public function testIsNotAuthenticated()
    {
        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $this->getSession(),
            $this->getRepository()
        );

        self::assertFalse($authentication->isAuthenticated($this->getRequest()));
    }

    public function testIsAuthenticated()
    {
        $user = $this->getUser('id1', 'username', '$2y$10$zXfRRDa2u9WxgB0noAnk1u281vVwNwjNcH5WCRdu8I70aBk23TS6G');

        $session = $this->getSession();
        $session->__storage[FormAuthentication::USER_KEY] = 'id1';

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository($user)
        );

        self::assertTrue($authentication->isAuthenticated($this->getRequest()));
    }

    /**
     * @return PasswordManagerInterface
     */
    private function getPasswordManager(): PasswordManagerInterface
    {
        $passwordManager = $this
            ->getMockBuilder(PasswordManagerInterface::class)
            ->setMethods(['verify'])
            ->getMockForAbstractClass()
        ;

        $passwordManager
            ->expects(self::any())
            ->method('verify')
            ->willReturnCallback(function (string $password, string $hash) {
                return $password === $hash;
            })
        ;

        return $passwordManager;
    }

    /**
     * @return SessionInterface
     */
    private function getSession(): SessionInterface
    {
        /** @var SessionInterface|\PHPUnit_Framework_MockObject_MockObject $session */
        $session = $this
            ->getMockBuilder(SessionInterface::class)
            ->setMethods(['has', 'get', 'set', 'remove'])
            ->getMockForAbstractClass()
        ;

        $session->__storage = [];

        $session
            ->expects(self::any())
            ->method('has')
            ->willReturnCallback(function (Request $request, string $key) use ($session) {
                return isset($session->__storage[$key]);
            })
        ;

        $session
            ->expects(self::any())
            ->method('get')
            ->willReturnCallback(function (Request $request, string $key) use ($session) {
                return isset($session->__storage[$key]) ? $session->__storage[$key] : null;
            })
        ;

        $session
            ->expects(self::any())
            ->method('set')
            ->willReturnCallback(function (Request $request, string $key, $value) use ($session) {
                $session->__storage[$key] = $value;
            })
        ;

        $session
            ->expects(self::any())
            ->method('remove')
            ->willReturnCallback(function (Request $request, string $key) use ($session) {
                unset($session->__storage[$key]);
            })
        ;

        return $session;
    }

    /**
     * @param UserInterface|null $user
     *
     * @return RepositoryInterface
     */
    private function getRepository(UserInterface $user = null): RepositoryInterface
    {
        /* @var RepositoryInterface|\PHPUnit_Framework_MockObject_MockObject $session */
        $repository = $this
            ->getMockBuilder(RepositoryInterface::class)
            ->setMethods(['find', 'findOneBy'])
            ->getMockForAbstractClass()
        ;

        $repository
            ->expects(self::any())
            ->method('find')
            ->willReturnCallback(function (string $id) use ($user) {
                if (null === $user) {
                    return null;
                }

                self::assertSame($user->getId(), $id);

                return $user;
            })
        ;

        $repository
            ->expects(self::any())
            ->method('findOneBy')
            ->willReturnCallback(function (array $criteria = []) use ($user) {
                if (null === $user) {
                    return null;
                }

                self::assertSame(['username' => $user->getUsername()], $criteria);

                return $user;
            })
        ;

        return $repository;
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

    /**
     * @param string $id
     * @param string $username
     * @param string $password
     *
     * @return UserInterface
     */
    private function getUser(string $id, string $username, string $password): UserInterface
    {
        /** @var UserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this
            ->getMockBuilder(UserInterface::class)
            ->setMethods(['getId', 'getUsername', 'getPassword'])
            ->getMockForAbstractClass()
        ;

        $user->expects(self::any())->method('getId')->willReturn($id);
        $user->expects(self::any())->method('getUsername')->willReturn($username);
        $user->expects(self::any())->method('getPassword')->willReturn($password);

        return $user;
    }
}
