<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 16/09/17
 * Time: 18:46
 */

namespace App\Blog\Controllers;


use App\Blog\Repositories\CategoryRepository;
use App\Blog\Repositories\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
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
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * BlogController constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param PostRepository $postRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(RendererInterface $renderer, Router $router, PostRepository $postRepository, CategoryRepository $categoryRepository) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function __invoke(Request $request) {
        if ($request->getAttribute('id')) {
            return $this->show($request);
        }
        return $this->index($request);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string {
        $params = $request->getQueryParams();
        $posts = $this->postRepository->findPublic()->paginate(12, $params['p'] ?? 1);
        $categories = $this->categoryRepository->findAll();
        $page = $params['p'] ?? 1;
        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'page'));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function show(Request $request) {

        $slug = $request->getAttribute('slug');
        $post = $this->postRepository->findWithCategory($request->getAttribute('id'));
        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }

        return $this->renderer->render('@blog/show', compact('post'));

    }

}