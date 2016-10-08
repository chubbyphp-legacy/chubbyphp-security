<?php

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Security\Authentication\Exception\AuthenticationExceptionInterface;
use Chubbyphp\Security\Authentication\Exception\InvalidPasswordException;
use Chubbyphp\Security\Authentication\Exception\MissingRequirementException;
use Chubbyphp\Security\Authentication\Exception\UserNotFoundException;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class FormAuthentication implements AuthenticationInterface
{
    /**
     * @var PasswordManagerInterface
     */
    private $passwordManager;

    /**
     * @var SessionInterface
     */
    private $session;

    const USER_KEY = 'u';

    /**
     * @var RepositoryInterface
     */
    private $userRepository;

    /**
     * @param PasswordManagerInterface $passwordManager
     * @param SessionInterface         $session
     * @param RepositoryInterface      $userRepository
     */
    public function __construct(
        PasswordManagerInterface $passwordManager,
        SessionInterface $session,
        RepositoryInterface $userRepository
    ) {
        $this->passwordManager = $passwordManager;
        $this->session = $session;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     *
     * @throws AuthenticationExceptionInterface
     */
    public function login(Request $request)
    {
        $data = $request->getParsedBody();
        $this->checkingRequirements($data);

        /** @var UserPasswordInterface $user */
        if (null === $user = $this->userRepository->findOneBy(['username' => $data['username']])) {
            throw UserNotFoundException::create(['username' => $data['username']]);
        }

        if (!$this->passwordManager->verify($data['password'], $user->getPassword())) {
            throw InvalidPasswordException::create();
        }

        $this->session->set($request, self::USER_KEY, $user->getId());
    }

    /**
     * @param array $data
     */
    private function checkingRequirements(array $data)
    {
        $fields = [];
        if (!isset($data['username'])) {
            $fields[] = 'username';
        }

        if (!isset($data['password'])) {
            $fields[] = 'password';
        }

        if ([] === $fields) {
            return;
        }

        throw MissingRequirementException::create($fields);
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
     * @return UserPasswordInterface|null
     */
    public function getAuthenticatedUser(Request $request)
    {
        if (!$this->session->has($request, self::USER_KEY)) {
            return null;
        }

        $id = $this->session->get($request, self::USER_KEY);

        $user = $this->userRepository->find($id);

        // remove from storage, but still a id in session
        if (null === $user) {
            $this->session->remove($request, self::USER_KEY);

            return null;
        }

        return $user;
    }
}
