<?php
/**
 * Created by PhpStorm.
 * UserInterface: amacabr2
 * Date: 20/09/17
 * Time: 09:56
 */

namespace Tests\Blog\Repositories;


use App\Blog\Entity\Post;
use App\Blog\Repositories\PostRepository;
use Framework\Database\NoRecordException;
use Tests\DatabaseTestCase;

class PostRepositoryTest extends DatabaseTestCase {

    /**
     * @var PostRepository
     */
    private $postRepository;

    public function setUp() {
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postRepository = new PostRepository($pdo);
    }

    public function testFind() {
        $this->seedDatabase($this->postRepository->getPdo());
        $post = $this->postRepository->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord() {
        $this->expectException(NoRecordException::class);
        $post = $this->postRepository->find(1000000);
    }

    public function testUpdate() {
        $this->seedDatabase($this->postRepository->getPdo());
        $this->postRepository->update(1, ['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postRepository->find(1);
        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testInsert() {
        $this->postRepository->insert(['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postRepository->find(1);
        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testDelete() {
        $this->postRepository->insert(['name' => 'Salut', 'slug' => 'demo']);
        $this->postRepository->insert(['name' => 'Salut', 'slug' => 'demo']);
        $count = $this->postRepository->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int)$count);
        $this->postRepository->delete($this->postRepository->getPdo()->lastInsertId());
        $count = $this->postRepository->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int)$count);
    }

}