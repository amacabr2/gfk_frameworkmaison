<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 14/09/17
 * Time: 13:18
 */

namespace App\Blog;

use App\Blog\Controllers\AdminBlogController;
use App\Blog\Controllers\BlogController;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class BlogModule extends Module {

    /**
     * @var Router
     */
    private $router;

    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS = __DIR__ . '/db/migrations';

    const SEEDS = __DIR__ . '/db/seeds';

    /**
     * BlogModule constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $prefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get($prefix, BlogController::class, 'blog.index');
        $router->get($prefix . '/{slug:[a-z0-9\-]+}-{id:[0-9]+}', BlogController::class, 'blog.show');
        if ($container->has('admin.prefix')) {
            $prefixAdmin = $container->get('admin.prefix');
            $router->get($prefixAdmin . '/posts', AdminBlogController::class, 'admin.blog.index');
            $router->get($prefixAdmin . '/posts/{id:\d+}', AdminBlogController::class, 'admin.blog.edit');
            $router->post($prefixAdmin . '/posts/{id:\d+}', AdminBlogController::class);
        }
    }



}