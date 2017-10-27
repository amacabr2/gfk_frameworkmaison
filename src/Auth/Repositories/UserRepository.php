<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/10/17
 * Time: 15:48
 */

namespace App\Auth\Repositories;


use App\Auth\Entity\User;
use Framework\Database\Repository;

class UserRepository extends Repository {

    /**
     * @var string
     */
    protected $table = "users";

    /**
     * @var string
     */
    protected $entity = User::class;

}