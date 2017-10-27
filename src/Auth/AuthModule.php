<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 27/10/17
 * Time: 13:10
 */

namespace App\Auth;


use App\Auth\Controllers\LoginController;
use App\Auth\Controllers\LogoutController;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class AuthModule extends Module {

    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS = __DIR__ . '/db/migrations';

    const SEEDS = __DIR__ . '/db/seeds';

    /**
     * AuthModule constructor.
     * @param ContainerInterface $container
     * @param Router $router
     * @param RendererInterface $renderer
     */
    public function __construct(ContainerInterface $container, Router $router, RendererInterface $renderer) {
        $renderer->addPath('auth', __DIR__ . '/views');
        $router->get($container->get('auth.login'), LoginController::class, 'auth.login');
        $router->post($container->get('auth.login'), LoginController::class);
        $router->post($container->get('auth.logout'), LogoutController::class, 'auth.logout');
    }


}