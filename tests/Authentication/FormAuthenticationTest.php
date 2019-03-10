<?php

namespace Chubbyphp\Tests\Security\Authentication;

use Chubbyphp\Security\Authentication\Exception\InvalidPasswordException;
use Chubbyphp\Security\Authentication\Exception\MissingRequirementException;
use Chubbyphp\Security\Authentication\Exception\UserNotFoundException;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use Chubbyphp\Security\UserInterface;
use Chubbyphp\Security\UserRepositoryInterface;
use Chubbyphp\Session\SessionInterface;
use Chubbyphp\Tests\Security\LoggerTestTrait;
use Psr\Http\Message\ServerRequestInterface as Request;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Security\Authentication\FormAuthentication
 */
final class FormAuthenticationTest extends TestCase
{
    use LoggerTestTrait;

    public function testLoginWithoutUsernameAndPasswordExpectException()
    {
        try {
            $logger = $this->getLogger();

            $authentication = new FormAuthentication(
                $this->getPasswordManager(),
                $this->getSession(),
                $this->getRepository(),
                $logger
            );

            $authentication->login($this->getRequest());
        } catch (MissingRequirementException $e) {
            self::assertSame('missing required fields username, password', $e->getMessage());

            self::assertCount(1, $logger->__logs);
            self::assertSame('warning', $logger->__logs[0]['level']);
            self::assertSame(
                'security.authentication.form: missing required fields {fields}',
                $logger->__logs[0]['message']
            );
            self::assertSame(['fields' => 'username, password'], $logger->__logs[0]['context']);

            return;
        }

        self::fail(sprintf('Expected exeption of type: %s', MissingRequirementException::class));
    }

    public function testLoginWithoutUsernameExpectException()
    {
        try {
            $logger = $this->getLogger();

            $authentication = new FormAuthentication(
                $this->getPasswordManager(),
                $this->getSession(),
                $this->getRepository(),
                $logger
            );

            $authentication->login($this->getRequest(['password' => 'password']));
        } catch (MissingRequirementException $e) {
            self::assertSame('missing required fields username', $e->getMessage());

            self::assertCount(1, $logger->__logs);
            self::assertSame('warning', $logger->__logs[0]['level']);
            self::assertSame(
                'security.authentication.form: missing required fields {fields}',
                $logger->__logs[0]['message']
            );
            self::assertSame(['fields' => 'username'], $logger->__logs[0]['context']);

            return;
        }

        self::fail(sprintf('Expected exeption of type: %s', MissingRequirementException::class));
    }

    public function testLoginWithoutUPasswordExpectException()
    {
        try {
            $logger = $this->getLogger();

            $authentication = new FormAuthentication(
                $this->getPasswordManager(),
                $this->getSession(),
                $this->getRepository(),
                $logger
            );

            $authentication->login($this->getRequest(['username' => 'username']));
        } catch (MissingRequirementException $e) {
            self::assertSame('missing required fields password', $e->getMessage());

            self::assertCount(1, $logger->__logs);
            self::assertSame('warning', $logger->__logs[0]['level']);
            self::assertSame(
                'security.authentication.form: missing required fields {fields}',
                $logger->__logs[0]['message']
            );
            self::assertSame(['fields' => 'password'], $logger->__logs[0]['context']);

            return;
        }

        self::fail(sprintf('Expected exeption of type: %s', MissingRequirementException::class));
    }

    public function testLoginUserNotFoundExpectException()
    {
        try {
            $logger = $this->getLogger();

            $authentication = new FormAuthentication(
                $this->getPasswordManager(),
                $this->getSession(),
                $this->getRepository(),
                $logger
            );

            $authentication->login($this->getRequest(['username' => 'username', 'password' => 'password']));
        } catch (UserNotFoundException $e) {
            self::assertSame('user not found with criteria username: username', $e->getMessage());

            self::assertCount(1, $logger->__logs);
            self::assertSame('warning', $logger->__logs[0]['level']);
            self::assertSame(
                'security.authentication.form: user not found with criteria {criteria}',
                $logger->__logs[0]['message']
            );
            self::assertSame(
                ['criteria' => $this->getCriteriaAsSting(['username' => 'username'])],
                $logger->__logs[0]['context']
            );

            return;
        }

        self::fail(sprintf('Expected exeption of type: %s', UserNotFoundException::class));
    }

    public function testLoginWithInvalidPasswordExpectException()
    {
        try {
            $logger = $this->getLogger();

            $user = $this->getUser('id1', 'username', 'invalidpassword');

            $authentication = new FormAuthentication(
                $this->getPasswordManager(),
                $this->getSession(),
                $this->getRepository($user),
                $logger
            );

            $authentication->login($this->getRequest(['username' => 'username', 'password' => 'password']));
        } catch (InvalidPasswordException $e) {
            self::assertSame('invalid password for user with criteria username: username', $e->getMessage());

            self::assertCount(1, $logger->__logs);
            self::assertSame('warning', $logger->__logs[0]['level']);
            self::assertSame(
                'security.authentication.form: invalid password for user with criteria {criteria}',
                $logger->__logs[0]['message']
            );
            self::assertSame(
                ['criteria' => $this->getCriteriaAsSting(['username' => 'username'])],
                $logger->__logs[0]['context']
            );

            return;
        }

        self::fail(sprintf('Expected exeption of type: %s', InvalidPasswordException::class));
    }

    public function testLogin()
    {
        $user = $this->getUser('id1', 'username', 'password');

        $session = $this->getSession();
        $logger = $this->getLogger();

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository($user),
            $logger
        );

        $authentication->login($this->getRequest(['username' => 'username', 'password' => 'password']));

        self::assertArrayHasKey(FormAuthentication::USER_KEY, $session->__storage);
        self::assertSame('id1', $session->__storage[FormAuthentication::USER_KEY]);

        self::assertCount(1, $logger->__logs);
        self::assertSame('info', $logger->__logs[0]['level']);
        self::assertSame(
            'security.authentication.form: login successful for user with id {id}',
            $logger->__logs[0]['message']
        );
        self::assertSame(['id' => 'id1'], $logger->__logs[0]['context']);
    }

    public function testLogoutWithoutSession()
    {
        $session = $this->getSession();
        $logger = $this->getLogger();

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository(),
            $logger
        );

        $authentication->logout($this->getRequest([]));

        self::assertArrayNotHasKey(FormAuthentication::USER_KEY, $session->__storage);

        self::assertCount(1, $logger->__logs);
        self::assertSame('warning', $logger->__logs[0]['level']);
        self::assertSame('security.authentication.form: logout not authenticated', $logger->__logs[0]['message']);
        self::assertSame([], $logger->__logs[0]['context']);
    }

    public function testLogout()
    {
        $session = $this->getSession();
        $session->__storage[FormAuthentication::USER_KEY] = 'id1';
        $logger = $this->getLogger();

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository(),
            $logger
        );

        $authentication->logout($this->getRequest([]));

        self::assertArrayNotHasKey(FormAuthentication::USER_KEY, $session->__storage);

        self::assertCount(1, $logger->__logs);
        self::assertSame('info', $logger->__logs[0]['level']);
        self::assertSame('security.authentication.form: logout user with id {id}', $logger->__logs[0]['message']);
        self::assertSame(['id' => 'id1'], $logger->__logs[0]['context']);
    }

    public function testGetAuthenticatedUserWithoutUserIdInSession()
    {
        $logger = $this->getLogger();

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $this->getSession(),
            $this->getRepository(),
            $logger
        );

        self::assertNull($authentication->getAuthenticatedUser($this->getRequest()));

        self::assertCount(1, $logger->__logs);
        self::assertSame('info', $logger->__logs[0]['level']);
        self::assertSame('security.authentication.form: not authenticated', $logger->__logs[0]['message']);
    }

    public function testGetAuthenticatedUserWithoutUserInReository()
    {
        $session = $this->getSession();
        $session->__storage[FormAuthentication::USER_KEY] = 'id1';
        $logger = $this->getLogger();

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository(),
            $logger
        );

        $authentication->getAuthenticatedUser($this->getRequest());

        self::assertCount(1, $logger->__logs);
        self::assertSame('warning', $logger->__logs[0]['level']);
        self::assertSame(
            'security.authentication.form: user with id {id} is not resolvable',
            $logger->__logs[0]['message']
        );
        self::assertSame(['id' => 'id1'], $logger->__logs[0]['context']);
    }

    public function testGetAuthenticatedUser()
    {
        $user = $this->getUser('id1', 'username', '$2y$10$zXfRRDa2u9WxgB0noAnk1u281vVwNwjNcH5WCRdu8I70aBk23TS6G');

        $session = $this->getSession();
        $session->__storage[FormAuthentication::USER_KEY] = 'id1';
        $logger = $this->getLogger();

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository($user),
            $logger
        );

        $user = $authentication->getAuthenticatedUser($this->getRequest());

        self::assertInstanceOf(UserInterface::class, $user);

        self::assertCount(1, $logger->__logs);
        self::assertSame('info', $logger->__logs[0]['level']);
        self::assertSame(
            'security.authentication.form: authenticated user with id {id}',
            $logger->__logs[0]['message']
        );
        self::assertSame(['id' => 'id1'], $logger->__logs[0]['context']);
    }

    public function testIsNotAuthenticated()
    {
        $logger = $this->getLogger();

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $this->getSession(),
            $this->getRepository(),
            $logger
        );

        self::assertFalse($authentication->isAuthenticated($this->getRequest()));

        self::assertCount(1, $logger->__logs);
        self::assertSame('info', $logger->__logs[0]['level']);
        self::assertSame('security.authentication.form: not authenticated', $logger->__logs[0]['message']);
    }

    public function testIsAuthenticated()
    {
        $user = $this->getUser('id1', 'username', '$2y$10$zXfRRDa2u9WxgB0noAnk1u281vVwNwjNcH5WCRdu8I70aBk23TS6G');

        $session = $this->getSession();
        $session->__storage[FormAuthentication::USER_KEY] = 'id1';
        $logger = $this->getLogger();

        $authentication = new FormAuthentication(
            $this->getPasswordManager(),
            $session,
            $this->getRepository($user),
            $logger
        );

        self::assertTrue($authentication->isAuthenticated($this->getRequest()));

        self::assertCount(1, $logger->__logs);
        self::assertSame('info', $logger->__logs[0]['level']);
        self::assertSame(
            'security.authentication.form: authenticated user with id {id}',
            $logger->__logs[0]['message']
        );
        self::assertSame(['id' => 'id1'], $logger->__logs[0]['context']);
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
     * @return UserRepositoryInterface
     */
    private function getRepository(UserInterface $user = null): UserRepositoryInterface
    {
        /* @var RepositoryInterface|\PHPUnit_Framework_MockObject_MockObject $session */
        $repository = $this
            ->getMockBuilder(UserRepositoryInterface::class)
            ->setMethods(['find', 'findByUsername'])
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
            ->method('findByUsername')
            ->willReturnCallback(function ($username) use ($user) {
                if (null === $user) {
                    return null;
                }

                self::assertSame($user->getUsername(), $username);

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

    /**
     * @param array $criteria
     *
     * @return string
     */
    private function getCriteriaAsSting(array $criteria): string
    {
        $criteriaString = '';
        foreach ($criteria as $key => $value) {
            $criteriaString .= $key.': '.$value.', ';
        }

        return substr($criteriaString, 0, -2);
    }
}
