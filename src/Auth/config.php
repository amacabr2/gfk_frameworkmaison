<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 13:25
 */

use App\Auth\AuthTwigExtension;
use App\Auth\DatabaseAuth;
use App\Auth\ForbiddenMiddleware;
use function DI\add;
use function DI\get;
use function DI\object;
use Framework\AuthInterface;

return [
    'auth.login' => '/login',
    'auth.logout' => '/logout',
    'twig.extensions' => add([
        get(AuthTwigExtension::class)
    ]),
    AuthInterface::class => get(DatabaseAuth::class),
    ForbiddenMiddleware::class => object()->constructorParameter('loginPath', get('auth.login'))
];