<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 22/09/17
 * Time: 13:43
 */

namespace App\Blog\Controllers;

use App\Blog\Repositories\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Stdlib\ResponseInterface;

class AdminBlogController {

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
            return $this->edit($request);
        }
        return $this->index($request);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string {
        $params =  $request->getQueryParams();
        $items = $this->postRepository->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/admin/index', compact('items'));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request) {
        $item = $this->postRepository->find($request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            $params = array_filter($request->getParsedBody(), function ($key) {
                return in_array($key, ['name', 'slug', 'content']);
            }, ARRAY_FILTER_USE_KEY);
            $this->postRepository->update($item->id, $params);
            return $this->redirect('admin.blog.index');
        }
        return $this->renderer->render('@blog/admin/edit', compact('item'));
    }

}