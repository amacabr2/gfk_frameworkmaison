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
        return parent::paginationQuery() . " ORDER BY created_at DESC";
    }

}