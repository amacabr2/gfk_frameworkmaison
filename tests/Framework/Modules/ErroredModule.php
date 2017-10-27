<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 14/09/17
 * Time: 13:42
 */

namespace Tests\Framework\Modules;

use Framework\Router;

class ErroredModule {

    public function __construct(Router $router) {
        $router->get('/demo', function () {
            return new \stdClass();
        }, 'demo');
    }

}