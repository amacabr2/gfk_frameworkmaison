<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 16/09/17
 * Time: 18:46
 */

namespace App\Blog\Controllers;


use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogController {
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * BlogController constructor.
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer) {
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request) {
        $slug = $request->getAttribute('slug');
        if ($slug) {
            return $this->show($slug);
        }
        return $this->index();
    }

    /**
     * @return string
     */
    public function index(): string {
        return $this->renderer->render('@blog/index');
    }

    /**
     * @param string $slug
     * @return string
     */
    public function show(string $slug): string {
        return $this->renderer->render('@blog/show', [
            'slug' =>$slug
        ]);
    }

}