<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 16/09/17
 * Time: 18:46
 */

namespace App\Blog\Controllers;


use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogController {

    use RouterAwareAction;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var Router
     */
    private $router;

    /**
     * BlogController constructor.
     * @param RendererInterface $renderer
     * @param \PDO $pdo
     * @param Router $router
     */
    public function __construct(RendererInterface $renderer, \PDO $pdo, Router $router) {
        $this->renderer = $renderer;
        $this->pdo = $pdo;
        $this->router = $router;
    }

    public function __invoke(Request $request) {
        if ($request->getAttribute('id')) {
            return $this->show($request);
        }
        return $this->index();
    }

    /**
     * @return string
     */
    public function index(): string {
        $posts = $this->pdo
            ->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10')
            ->fetchAll();
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function show(Request $request) {

        $slug = $request->getAttribute('slug');
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $query->execute([
            'id' => $request->getAttribute('id')
        ]);
        $post = $query->fetch();

        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }

        return $this->renderer->render('@blog/show', compact('post'));

    }

}