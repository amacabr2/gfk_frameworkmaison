<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 14/09/17
 * Time: 13:18
 */

namespace App\Blog;

use App\Blog\Controllers\BlogController;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class BlogModule extends Module {

    /**
     * @var Router
     */
    private $router;

    const DEFINITIONS = __DIR__ . '/config.php';

    /**
     * BlogModule constructor.
     * @param string $prefix
     * @param Router $router
     * @param RendererInterface $renderer
     */
    public function __construct(string $prefix, Router $router, RendererInterface $renderer) {
        $renderer->addPath('blog', __DIR__ . '/views');
        $router->get($prefix, BlogController::class, 'blog.index');
        $router->get($prefix . '{slug:[a-z0-9\-]+}', BlogController::class, 'blog.show');
        $this->router = $router;
    }



}