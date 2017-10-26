<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 08/10/17
 * Time: 14:03
 */

namespace App\Blog\Controllers;


use App\Blog\Repositories\CategoryRepository;
use App\Blog\Repositories\PostRepository;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryController {

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * CategoryController constructor.
     * @param RendererInterface $renderer
     * @param PostRepository $postRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(RendererInterface $renderer, PostRepository $postRepository, CategoryRepository $categoryRepository) {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Request $request
     * @return string
     */
    function __invoke(Request $request) {
        if ($request->getAttribute('slug')) {
            return $this->index($request);
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string {
        $params = $request->getQueryParams();
        $category = $this->categoryRepository->findBy('slug', $request->getAttribute('slug'));
        $posts = $this->postRepository->findPublicForCategory($category->id)->paginate(12, $params['p'] ?? 1, $category->id);
        $categories = $this->categoryRepository->findAll();
        $page = $params['p'] ?? 1;
        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'category', 'page'));
    }

}