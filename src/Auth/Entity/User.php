<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 27/10/17
 * Time: 13:19
 */

namespace App\Auth\Entity;


use Framework\Auth\UserInterface;

class User implements UserInterface {

    public $id;

    public $username;

    public $email;

    public $password;

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array{
        // TODO: Implement getRoles() method.
    }
}