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

    protected function paginationQuery() {
        return "SELECT p.id, p.name, c.name category_name
                FROM {$this->table} AS p
                LEFT JOIN categories AS c ON p.category_id = c.id
                ORDER BY created_at DESC";
    }

}