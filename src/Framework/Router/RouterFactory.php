<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 13/10/17
 * Time: 11:16
 */

namespace Framework\Router;


use Framework\Router;
use Psr\Container\ContainerInterface;

class RouterFactory {

    public function __invoke(ContainerInterface $container) {
        $cache = null;
        if ($container->get('env') === 'production') {
            $cache = 'tmp/routes';
        }
        return new Router($cache);
    }

}