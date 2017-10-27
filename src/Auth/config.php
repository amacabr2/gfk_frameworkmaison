<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 13:25
 */

use App\Auth\DatabaseAuth;
use Framework\AuthInterface;

return [
    'auth.login' => '/login',
    'auth.logout' => '/logout',
    AuthInterface::class => \DI\get(DatabaseAuth::class)
];