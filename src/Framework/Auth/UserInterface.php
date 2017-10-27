<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 27/10/17
 * Time: 13:02
 */

namespace Framework\Auth;


interface UserInterface {

    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @return string[]
     */
    public function getRoles(): array;

}