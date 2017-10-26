<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 19/09/17
 * Time: 10:11
 */

namespace Framework\Database;


use Pagerfanta\Adapter\AdapterInterface;

class PaginateQuery implements AdapterInterface {

    /**
     * @var Query
     */
    private $query;

    /**
     * PaginateQuery constructor.
     * @param Query $query
     */
    public function __construct(Query $query) {
        $this->query = $query;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults(): int {
        return $this->query->count();
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length): array {
       $query = clone $this->query;
       return $query->limit($length, $offset)->fetchAll();
    }
}