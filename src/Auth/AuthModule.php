<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 27/10/17
 * Time: 13:10
 */

namespace App\Auth;


use Framework\Module;
use Psr\Container\ContainerInterface;

class AuthModule extends Module {

    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS = __DIR__ . '/db/migrations';

    const SEEDS = __DIR__ . '/db/seeds';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AuthModule constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }


}