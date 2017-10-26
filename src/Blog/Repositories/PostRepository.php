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

    public function findPublic(): Query {
        $category = new CategoryRepository($this->pdo);
        return $this->makeQuery()
            ->join($category->getTable() . ' as c', 'c.id = p.category_id')
            ->select('p.*, c.name as category_name, c.slug as category_slug')
            ->order('p.created_at DESC');
    }
}