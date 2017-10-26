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
use Framework\Database\Query;
use Framework\Database\Repository;
use Pagerfanta\Pagerfanta;

class PostRepository extends Repository {

    protected $entity = Post::class;

    protected $table = 'posts';

    public function findAll(): Query {
        $category = new CategoryRepository($this->pdo);
        return $this->makeQuery()
            ->join($category->getTable() . ' as c', 'c.id = p.category_id')
            ->select('p.*, c.name as category_name, c.slug as category_slug')
            ->order('p.created_at DESC');
    }

    /**
     * @return Query
     */
    public function findPublic(): Query {
        return $this->findAll();
    }

    /**
     * @param int $id
     * @return Query
     */
    public function findPublicForCategory(int $id): Query {
        return $this->findPublic()->where("p.category_id = $id");
    }

    /**
     * @param int $id
     * @return Post
     */
    public function findWithCategory(int $id): Post {
        return $this->findPublic()->where("p.id = $id")->fetch();
    }
}