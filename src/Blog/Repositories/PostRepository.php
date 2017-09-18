<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 18/09/17
 * Time: 13:03
 */

namespace App\Blog\Repositories;


class PostRepository {

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * PostRepository constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * @return \stdClass[]
     */
    public function findPaginated(): array {
        return $this->pdo
            ->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10')
            ->fetchAll();
    }

    /**
     * @param int $id
     * @return \stdClass
     */
    public function find(int $id): \stdClass {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $query->execute([
            'id' => $id
        ]);
        return $query->fetch();
    }

}