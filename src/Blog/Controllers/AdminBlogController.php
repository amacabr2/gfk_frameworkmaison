<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 22/09/17
 * Time: 13:43
 */

namespace App\Blog\Controllers;

use App\Blog\Entity\Post;
use App\Blog\Repositories\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator;
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
     * @var FlashService
     */
    private $flash;

    /**
     * BlogController constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param PostRepository $postRepository
     * @param FlashService $flash
     */
    public function __construct(RendererInterface $renderer, Router $router, PostRepository $postRepository, FlashService $flash) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->postRepository = $postRepository;
        $this->flash = $flash;
    }

    public function __invoke(Request $request) {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
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
    public function create(Request $request) {
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postRepository->insert($params);
                $this->flash->success('L\'article a bien été créé');
                return $this->redirect('blog.admin.index');
            }
            $item = $params;
            $errors = $validator->getErrors();
        }
        $item = new Post();
        $item->created_at = new \DateTime();
        return $this->renderer->render('@blog/admin/create', compact('item', 'errors'));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request) {
        $item = $this->postRepository->find($request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params['updated_at'] = date('H-m-d H:i:s');
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postRepository->update($item->id, $params);
                $this->flash->success('L\'article a bien été modifié');
                return $this->redirect('blog.admin.index');
            }
            $errors = $validator->getErrors();
            $params['id'] = $item->id;
            $item = $params;
        }
        return $this->renderer->render('@blog/admin/edit', compact('item', 'errors'));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function delete(Request $request) {
        $this->postRepository->delete($request->getAttribute('id'));
        $this->flash->success('L\'article a bien été supprimé');
        return $this->redirect('blog.admin.index');
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getParams(Request $request): array {
        $params =  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('H-m-d H:i:s'),
        ]);
    }

    private function getValidator(Request $request) {
        return (new Validator($request->getParsedBody()))
            ->required('content', 'name', 'slug', 'created_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->slug('slug')
            ->dateTime('created_at');
    }

}