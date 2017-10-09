<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 09/10/17
 * Time: 11:11
 */

namespace App\Blog;


use App\Admin\AdminWidgetInterface;
use App\Blog\Repositories\PostRepository;
use Framework\Renderer\RendererInterface;

class BlogWidget implements AdminWidgetInterface {
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * BlogWidget constructor.
     * @param RendererInterface $renderer
     * @param PostRepository $postRepository
     */
    public function __construct(RendererInterface $renderer, PostRepository $postRepository) {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
    }

    /**
     * @return string
     */
    public function render(): string {
        $count = $this->postRepository->count();
        return $this->renderer->render('@blog/admin/widget', compact('count'));
    }

    public function renderMenu(): string {
        return $this->renderer->render('@blog/admin/menu');
    }
}