<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 13/10/17
 * Time: 15:09
 */

namespace Tests\Framework\Database;


use Framework\Database\Query;
use Tests\DatabaseTestCase;

class QueryTest extends DatabaseTestCase {

    public function testSimpleQuery() {
        $query = (new Query())->from('posts')->select('name');
        $this->assertEquals("SELECT name FROM posts", (string)$query);
    }

    public function testWithWhere() {
        $query1 = (new Query())
            ->from('posts', 'p')
            ->where('a = :a OR b = :b', 'c = :c');
        $query2 = (new Query())
            ->from('posts', 'p')
            ->where('a = :a OR b = :b')
            ->where('c = :c');
        $this->assertEquals("SELECT * FROM posts as p WHERE (a = :a OR b = :b) AND (c = :c)", (string)$query1);
        $this->assertEquals("SELECT * FROM posts as p WHERE (a = :a OR b = :b) AND (c = :c)", (string)$query2);
    }

    public function testFetchAll() {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $query = (new Query($pdo))->from('posts', 'p')->count();
        $this->assertEquals(100, $query);
        $posts = (new Query($pdo))
            ->from('posts', 'p')
            ->where('p.id < :number')
            ->params([
                'number' => 30
            ])->count();
        $this->assertEquals(29, $posts);
    }

    public function testHydrateEntity() {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = (new Query($pdo))
            ->from('posts', 'p')
            ->into(Demo::class)
            ->all();
        $this->assertEquals('demo', substr($posts[0]->getSlug(), -4));
    }

    public function testLazyHydrate() {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = (new Query($pdo))
            ->from('posts', 'p')
            ->into(Demo::class)
            ->all();
        $post1 = $posts[0];
        $post2 = $posts[0];
        $this->assertSame($post1, $post2);
    }

}