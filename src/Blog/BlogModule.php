<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 14/09/17
 * Time: 13:18
 */

namespace App\Blog;

use Framework\Renderer;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule {

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * BlogModule constructor.
     * @param Router $router
     */
    public function __construct(Router $router) {
        $this->renderer = new Renderer();
        $this->renderer->addPath('blog', '/views');
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z0-9\-]+}', [$this, 'show'], 'blog.show');
    }

    public function index(ServerRequestInterface $request): string {
        return $this->renderer->render('@blog/index');
    }

    public function show(ServerRequestInterface $request): string {
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);
    }

}