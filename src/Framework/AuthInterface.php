<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 27/10/17
 * Time: 13:01
 */

namespace Framework;


use Framework\Auth\UserInterface;

interface AuthInterface {

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

}