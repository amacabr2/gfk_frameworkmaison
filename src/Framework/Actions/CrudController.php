<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 04/10/17
 * Time: 11:28
 */

namespace Framework\Actions;

use Framework\Database\Hydrator;
use Framework\Database\Repository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Stdlib\ResponseInterface;

class CrudController {

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
     * @var Repository
     */
    protected $repository;

    /**
     * @var FlashService
     */
    private $flash;

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var array
     */
    protected $messages = [
        'create' => "L'élément a bien été crée",
        'edit' => "L'élément a bien été modifié",
        'delete' => "L'élément a bien été supprimé",
    ];

    /**
     * BlogController constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param Repository $repository
     * @param FlashService $flash
     */
    public function __construct(RendererInterface $renderer, Router $router, Repository $repository, FlashService $flash) {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->repository = $repository;
        $this->flash = $flash;
    }

    public function __invoke(Request $request) {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
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
        $params = $request->getQueryParams();
        $items = $this->repository->findAll()->paginate(12, $params['p'] ?? 1);
        return $this->renderer->render("$this->viewPath/index", compact('items'));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request) {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->repository->insert($this->getParams($request, $item));
                $this->flash->success($this->messages['create']);
                return $this->redirect("$this->routePrefix.index");
            }
            Hydrator::hydrate($request->getParsedBody(), $item);
            $errors = $validator->getErrors();
        }
        return $this->renderer->render("$this->viewPath/create", $this->formParams(compact('item', 'errors')));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request) {
        $item = $this->repository->find($request->getAttribute('id'));
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->repository->update($item->id, $this->getParams($request, $item));
                $this->flash->success($this->messages['edit']);
                return $this->redirect("$this->routePrefix.index");
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item);
        }
        return $this->renderer->render("$this->viewPath/edit", $this->formParams(compact('item', 'errors')));
    }

    /**
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function delete(Request $request) {
        $this->repository->delete($request->getAttribute('id'));
        $this->flash->success($this->messages['delete']);
        return $this->redirect("$this->routePrefix.index");
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request, $item): array {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param Request $request
     * @return Validator
     */
    protected function getValidator(Request $request) {
        return (new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles())));
    }

    /**
     * @return array
     */
    protected function getNewEntity() {
        return [];
    }

    /**
     * @param $params
     * @return array
     */
    protected function formParams(array $params): array {
        return $params;
    }

}