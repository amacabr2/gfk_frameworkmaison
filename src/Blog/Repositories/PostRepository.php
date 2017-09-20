<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 18/09/17
 * Time: 13:03
 */

namespace App\Blog\Repositories;


use App\Blog\Entity\Post;
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
            'SELECT * FROM posts ORDER BY created_at DESC',
            'SELECT COUNT(id) FROM posts',
            Post::class
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * @param int $id
     * @return Post|null
     */
    public function find(int $id): ?Post {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id = :id');
        $query->execute([
            'id' => $id
        ]);
        $query->setFetchMode(\PDO::FETCH_CLASS, Post::class);
        return $query->fetch() ?: null;
    }

}