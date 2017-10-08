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
use Framework\Database\Repository;
use Pagerfanta\Pagerfanta;

class PostRepository extends Repository {

    protected $entity = Post::class;

    protected $table = 'posts';

    /**
     * @return string
     */
    protected function paginationQuery() {
        return "SELECT p.id, p.name, c.name category_name
                FROM {$this->table} AS p
                LEFT JOIN categories AS c ON p.category_id = c.id
                ORDER BY created_at DESC";
    }

    /**
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta {
        $query = new PaginateQuery(
            $this->pdo,
            "SELECT p.*, c.name AS category_name, c.slug AS categoty_slug
                    FROM posts AS p 
                    LEFT JOIN categories AS c ON c.id = p.category_id 
                    ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta {
        $query = new PaginateQuery(
            $this->pdo,
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                    FROM posts AS p 
                    LEFT JOIN categories AS c ON c.id = p.category_id 
                    WHERE p.category_id = :category
                    ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table} WHERE category_id = :category",
            $this->entity,
            ['category' => $categoryId]
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findWithCategory($getAttribute) {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $query->execute([
            'id' => $id
        ]);
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $record =  $query->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

}