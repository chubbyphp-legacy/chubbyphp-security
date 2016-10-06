<?php

namespace Chubbyphp\Security\Authentication\Form;

use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authentication\Exception\EmptyPasswordException;
use Chubbyphp\Security\Authentication\Exception\InvalidPasswordException;
use Chubbyphp\Security\Authentication\Exception\UserNotFoundException;
use Chubbyphp\Security\Authentication\UserCredentialsInterface;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class FormAuthentication implements AuthenticationInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var RepositoryInterface
     */
    private $userRepository;

    /**
     * @param SessionInterface    $session
     * @param RepositoryInterface $userRepository
     */
    public function __construct(SessionInterface $session, RepositoryInterface $userRepository)
    {
        $this->session = $session;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     *
     * @throws InvalidPasswordException
     * @throws UserNotFoundException
     */
    public function login(Request $request)
    {
        $data = $request->getParsedBody();

        /** @var UserCredentialsInterface $user */
        if (null === $user = $this->userRepository->findOneBy(['username' => $data['username']])) {
            throw UserNotFoundException::create($data['username']);
        }

        if (!password_verify($data['password'], $user->getPassword())) {
            throw InvalidPasswordException::create();
        }

        $this->session->set($request, self::USER_KEY, $user->getId());
    }

    /**
     * @param Request $request
     */
    public function logout(Request $request)
    {
        $this->session->remove($request, self::USER_KEY);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isAuthenticated(Request $request): bool
    {
        return null !== $this->getAuthenticatedUser($request);
    }

    /**
     * @param Request $request
     *
     * @return UserCredentialsInterface|null
     */
    public function getAuthenticatedUser(Request $request)
    {
        if (!$this->session->has($request, self::USER_KEY)) {
            return null;
        }

        $id = $this->session->get($request, self::USER_KEY);

        return $this->userRepository->find($id);
    }

    /**
     * @param string $password
     *
     * @return string
     *
     * @throws EmptyPasswordException
     */
    public function hashPassword(string $password): string
    {
        if ('' === $password) {
            throw EmptyPasswordException::create();
        }

        return password_hash($password, PASSWORD_DEFAULT);
    }
}
