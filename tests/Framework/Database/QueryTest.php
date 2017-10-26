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

    public function testFetchfetchAll() {
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
            ->fetchAll();
        $this->assertEquals('demo', substr($posts[0]->getSlug(), -4));
    }

    public function testLazyHydrate() {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = (new Query($pdo))
            ->from('posts', 'p')
            ->into(Demo::class)
            ->fetchAll();
        $post1 = $posts[0];
        $post2 = $posts[0];
        $this->assertSame($post1, $post2);
    }

    public function testJoinQuery() {
        $query = (new Query())
            ->from('posts', 'p')
            ->select('name')
            ->join('categories as c', 'c.id = p.category_id')
            ->join('categories as c2', 'c2.id = p.category_id', 'inner');
        $this->assertEquals('SELECT name FROM posts as p LEFT JOIN categories as c ON c.id = p.category_id INNER JOIN categories as c2 ON c2.id = p.category_id', (string)$query);
    }

    public function testLimitOrder() {
        $query = (new Query())
            ->from('posts', 'p')
            ->select('name')
            ->order('id DESC')
            ->order('name ASC')
            ->limit(10, 5)
            ->join('categories as c', 'c.id = p.category_id');
        $this->assertEquals('SELECT name FROM posts as p LEFT JOIN categories as c ON c.id = p.category_id ORDER BY id DESC, name ASC LIMIT 5, 10', (string)$query);
    }
}