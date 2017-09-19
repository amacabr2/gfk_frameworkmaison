<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 18/09/17
 * Time: 13:03
 */

namespace App\Blog\Repositories;


use Framework\Database\PaginateQuery;
use Pagerfanta\Pagerfanta;

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
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta {
        $query = new PaginateQuery(
            $this->pdo,
            'SELECT * FROM posts ORDER BY created_at',
            'SELECT COUNT(id) FROM posts'
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
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