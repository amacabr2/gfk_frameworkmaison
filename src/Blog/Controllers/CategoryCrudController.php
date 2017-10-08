<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 04/10/17
 * Time: 13:44
 */

namespace App\Blog\Controllers;

use App\Blog\Repositories\CategoryRepository;
use Framework\Actions\CrudController;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryCrudController extends CrudController {

    protected $viewPath = "@blog/admin/categories";

    protected $routePrefix = "blog.admin.category";

    protected $messages = [
        'create' => "La catégorie a bien été crée",
        'edit' => "La catégorie a bien été modifié",
        'delete' => "La catégorie a bien été supprimé",
    ];

    public function __construct(RendererInterface $renderer, Router $router, CategoryRepository $categoryRepository, FlashService $flash) {
        parent::__construct($renderer, $router, $categoryRepository, $flash);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request): array {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param Request $request
     * @return \Framework\Validator
     */
    protected function getValidator(Request $request) {
        return parent::getValidator($request)
            ->required( 'name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->slug('slug')
            ->unique('slug', $this->repository->getTable(), $this->repository->getPdo(), $request->getAttribute('id'));
    }

}