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
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $countQuery;

    /**
     * PaginateQuery constructor.
     * @param \PDO $pdo
     * @param string $query
     * @param string $countQuery
     */
    public function __construct(\PDO $pdo, string $query, string $countQuery) {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults(): int {
        return $this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length) {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        $statement->bindParam('offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam('length', $length, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }
}