<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 22/09/17
 * Time: 13:24
 */

namespace App\Admin;


use Framework\Module;
use Framework\Renderer\RendererInterface;

class AdminModule extends Module {

    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(RendererInterface $renderer) {
        $renderer->addPath('admin', __DIR__ . '/views');
    }

}