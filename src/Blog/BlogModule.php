<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 14/09/17
 * Time: 13:18
 */

namespace App\Blog;

use App\Blog\Controllers\CategoryController;
use App\Blog\Controllers\CategoryCrudController;
use App\Blog\Controllers\PostCrudController;
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

        $prefixBlog = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get($prefixBlog, BlogController::class, 'blog.index');
        $router->get($prefixBlog . '/{slug:[a-z0-9\-]+}-{id:[0-9]+}', BlogController::class, 'blog.show');
        $router->get($prefixBlog . '/category/{slug:[a-z0-9\-]+}', CategoryController::class, 'blog.category');

        if ($container->has('admin.prefix')) {
            $prefixAdmin = $container->get('admin.prefix');
            $router->crud("$prefixAdmin/posts", PostCrudController::class, 'blog.admin.post');
            $router->crud("$prefixAdmin/categories", CategoryCrudController::class, 'blog.admin.category');
        }

    }



}