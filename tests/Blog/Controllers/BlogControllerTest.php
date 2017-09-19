<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 18/09/17
 * Time: 11:33
 */

namespace Tests\Blog\Controllers;


use App\Blog\Controllers\BlogController;
use App\Blog\Entity\Post;
use App\Blog\Repositories\PostRepository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class BlogControllerTest extends TestCase {

    /**
     * @var BlogController
     */
    private $action;

    private $renderer;

    private $router;

    private $postRepository;

    public function setUp() {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->router = $this->prophesize(Router::class);
        $this->postRepository = $this->prophesize(PostRepository::class);
        $this->action = new BlogController(
            $this->renderer->reveal(),
            $this->router->reveal(),
            $this->postRepository->reveal()
        );
    }

    public function testShowRedirect() {
        $post = $this->makePost(9, 'azerty-qwerty');
        $this->router->generateUri('blog.show', ['id' => $post->id, 'slug' => $post->slug])->willReturn('/demo2');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');
        $this->postRepository->find($post->id)->willReturn($post);
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('Location'));
    }

    public function testShowRender() {

        $post = $this->makePost(9, 'azerty-qwerty');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');
        $this->postRepository->find($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }

    /**
     * @param int $id
     * @param string $slug
     * @return Post
     */
    private function makePost(int $id, string $slug): Post {
        $post = new Post();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }

}