<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 14/09/17
 * Time: 13:18
 */

namespace App\Blog;

use Framework\Renderer\PHPRenderer;
use Framework\Renderer\TwigRenderer;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule {

    /**
     * @var PHPRenderer
     */
    private $renderer;
    /**
     * @var Router
     */
    private $router;

    /**
     * BlogModule constructor.
     * @param Router $router
     * @param TwigRenderer $renderer
     */
    public function __construct(Router $router, TwigRenderer $renderer) {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z0-9\-]+}', [$this, 'show'], 'blog.show');
        $this->router = $router;
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