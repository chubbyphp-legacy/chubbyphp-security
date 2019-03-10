<?php

declare(strict_types=1);

namespace Chubbyphp\Security\Authentication;

use Chubbyphp\Security\Authentication\Exception\AuthenticationExceptionInterface;
use Chubbyphp\Security\Authentication\Exception\InvalidPasswordException;
use Chubbyphp\Security\Authentication\Exception\MissingRequirementException;
use Chubbyphp\Security\Authentication\Exception\UserNotFoundException;
use Chubbyphp\Security\UserRepositoryInterface;
use Chubbyphp\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
     * @var UserRepositoryInterface|mixed
     */
    private $userRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PasswordManagerInterface      $passwordManager
     * @param SessionInterface              $session
     * @param UserRepositoryInterface|mixed $userRepository
     */
    public function __construct(
        PasswordManagerInterface $passwordManager,
        SessionInterface $session,
        $userRepository,
        LoggerInterface $logger = null
    ) {
        $this->passwordManager = $passwordManager;
        $this->session = $session;
        $this->userRepository = $userRepository;
        $this->logger = $logger ?? new NullLogger();
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
        if (null === $user = $this->findByUsername($data['username'])) {
            $this->logger->warning(
                'security.authentication.form: user not found with criteria {criteria}',
                ['criteria' => $this->getCriteriaAsSting(['username' => $data['username']])]
            );

            throw UserNotFoundException::create(['username' => $data['username']]);
        }

        if (!$this->passwordManager->verify($data['password'], $user->getPassword())) {
            $this->logger->warning(
                'security.authentication.form: invalid password for user with criteria {criteria}',
                ['criteria' => $this->getCriteriaAsSting(['username' => $data['username']])]
            );

            throw InvalidPasswordException::create(['username' => $data['username']]);
        }

        $this->logger->info(
            'security.authentication.form: login successful for user with id {id}', ['id' => $user->getId()]
        );

        $this->session->set($request, self::USER_KEY, $user->getId());
    }

    /**
     * @param string $username
     *
     * @return UserInterface|null
     */
    private function findByUsername(string $username)
    {
        if ($this->userRepository instanceof UserRepositoryInterface) {
            return $this->userRepository->findByUsername($username);
        }

        return $this->userRepository->findOneBy(['username' => $username]);
    }

    /**
     * @param string $id
     *
     * @return UserInterface|null
     */
    private function find(string $id)
    {
        return $this->userRepository->find($id);
    }

    /**
     * @param array|object|null $data
     */
    private function checkingRequirements($data)
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

        $this->logger->warning(
            'security.authentication.form: missing required fields {fields}', ['fields' => implode(', ', $fields)]
        );

        throw MissingRequirementException::create($fields);
    }

    /**
     * @param Request $request
     */
    public function logout(Request $request)
    {
        if (!$this->checkForUserIdWithinSession($request)) {
            $this->logger->warning('security.authentication.form: logout not authenticated');

            return;
        }

        $id = $this->getUserIdFromSession($request);

        $this->logger->info(
            'security.authentication.form: logout user with id {id}', ['id' => $id]
        );

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
        if (!$this->checkForUserIdWithinSession($request)) {
            $this->logger->info('security.authentication.form: not authenticated');

            return null;
        }

        $id = $this->getUserIdFromSession($request);

        if (null === $user = $this->find($id)) {
            $this->logger->warning('security.authentication.form: user with id {id} is not resolvable', ['id' => $id]);
            $this->session->remove($request, self::USER_KEY);

            return null;
        }

        $this->logger->info('security.authentication.form: authenticated user with id {id}', ['id' => $id]);

        return $user;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function checkForUserIdWithinSession(Request $request): bool
    {
        return $this->session->has($request, self::USER_KEY);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getUserIdFromSession(Request $request): string
    {
        return $this->session->get($request, self::USER_KEY);
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
