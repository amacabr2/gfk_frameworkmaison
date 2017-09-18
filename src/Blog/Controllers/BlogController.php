<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 16/09/17
 * Time: 18:46
 */

namespace App\Blog\Controllers;


use App\Blog\Repositories\PostRepository;
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
     * @var Router
     */
    private $router;
    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * BlogController constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param PostRepository $postRepository
     */
    public function __construct(RendererInterface $renderer, Router $router, PostRepository $postRepository) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postRepository = $postRepository;
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
        $posts = $this->postRepository->findPaginated();
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function show(Request $request) {

        $slug = $request->getAttribute('slug');
        $post = $this->postRepository->find($request->getAttribute('id'));

        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }

        return $this->renderer->render('@blog/show', compact('post'));

    }

}