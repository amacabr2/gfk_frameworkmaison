<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 13:24
 */

namespace App\Auth;

use App\Auth\Entity\User;
use App\Auth\Repositories\UserRepository;
use Framework\Auth\UserInterface;
use Framework\AuthInterface;
use Framework\Database\NoRecordException;
use Framework\Session\SessionInterface;

class DatabaseAuth implements AuthInterface {

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var User
     */
    private $user;

    /**
     * DatabaseAuth constructor.
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     */
    public function __construct(UserRepository $userRepository, SessionInterface $session){
        $this->userRepository = $userRepository;
        $this->session = $session;
    }


    /**
     * @param string $username
     * @param string $password
     * @return User|null
     */
    public function login(string $username, string $password): ?User {
        if (empty($username) || empty($password)) {
            return null;
        }

        /** @var User $user */
        $user = $this->userRepository->findBy('username', $username);
        if ($user && password_verify($password, $user->password)) {
            $this->session->set('auth.user', $user->id);
            return $user;
        }

        return null;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface {
        if ($this->user) {
            return $this->user;
        }

        $userId = $this->session->get('auth.user');
        if ($userId) {
           try {
               $this->user = $this->userRepository->find($userId);
               return $this->user;
           } catch (NoRecordException $exception) {
               $this->session->delete('auth.user');
               return null;
           }
        }

        return null;
    }
}