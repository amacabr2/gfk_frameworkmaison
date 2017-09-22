<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 20/09/17
 * Time: 09:56
 */

namespace Tests\Blog\Repositories;


use App\Blog\Entity\Post;
use App\Blog\Repositories\PostRepository;
use Tests\DatabaseTestCase;

class PostRepositoryTest extends DatabaseTestCase {

    /**
     * @var PostRepository
     */
    private $postRepository;

    public function setUp() {
        parent::setUp();
        $this->postRepository = new PostRepository($this->pdo);
    }

    public function testFind() {
        $this->seedDatabase();
        $post = $this->postRepository->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord() {
        $post = $this->postRepository->find(1000000);
        $this->assertNull($post);
    }

    public function testUpdate() {
        $this->seedDatabase();
        $this->postRepository->update(1, ['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postRepository->find(1);
        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

}