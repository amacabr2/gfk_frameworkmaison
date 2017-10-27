<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 13:24
 */

namespace App\Auth;

use Framework\Auth\UserInterface;
use Framework\AuthInterface;

class DatabaseAuth implements AuthInterface {

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface {
        return null;
    }
}