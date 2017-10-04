<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 04/10/17
 * Time: 11:10
 */

namespace Tests\Framework\Database;


use Framework\Database\Repository;
use PDO;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase {

    /**
     * @var Repository
     */
    private $repository;

    public function setUp() {

        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
        $pdo->exec('CREATE TABLE test (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          name VARCHAR(255)
        )');

        $this->repository = new Repository($pdo);
        $reflection = new \ReflectionClass($this->repository);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        $property->setValue($this->repository, 'test');

        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');

    }

    public function testFind() {
        $test = $this->repository->find(1);
        $this->assertInstanceOf(\stdClass::class, $test);
        $this->assertEquals('a1', $test->name);
    }

    public function testFindList() {
        $test = $this->repository->findList();
        $this->assertEquals(['1' => 'a1', '2' => 'a2'], $test);
    }

    public function testExists() {
        $this->assertTrue($this->repository->exists(1));
        $this->assertTrue($this->repository->exists(2));
        $this->assertFalse($this->repository->exists(12345));
    }

}