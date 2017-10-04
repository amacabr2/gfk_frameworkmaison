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
use DateTime;
use Framework\Actions\CrudController;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudController extends CrudController {

    protected $viewPath = "@blog/admin/posts";

    protected $routePrefix = "blog.admin.post";

    protected $messages = [
        'create' => "L'article a bien été crée",
        'edit' => "L'article a bien été modifié",
        'delete' => "L'article a bien été supprimé",
    ];

    public function __construct(RendererInterface $renderer, Router $router, PostRepository $postRepository, FlashService $flash) {
        parent::__construct($renderer, $router, $postRepository, $flash);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request): array {
        $params =  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('H-m-d H:i:s'),
        ]);
    }

    /**
     * @param Request $request
     * @return \Framework\Validator
     */
    protected function getValidator(Request $request) {
        return parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->slug('slug')
            ->dateTime('created_at');
    }

    /**
     * @return Post
     */
    protected function getNewEntity() {
        $post = new Post();
        $post->created_at = new DateTime();
        return $post;
    }

}