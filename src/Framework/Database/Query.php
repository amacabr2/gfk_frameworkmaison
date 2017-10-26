<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 13/10/17
 * Time: 15:10
 */

namespace Framework\Database;


use Pagerfanta\Pagerfanta;
use PDO;

class Query {

    /**
     * @var string[]
     */
    private $from;

    /**
     * @var string
     */
    private $select;

    /**
     * @var string[]
     */
    private $where = [];

    /**
     * @var mixed
     */
    private $entity;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var array
     */
    private $order = [];

    /**
     * @var array
     */
    private $joins;

    /**
     * @var string
     */
    private $limit;

    /**
     * Query constructor.
     * @param PDO $pdo
     */
    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    /**
     * @param string $table
     * @param string $alias
     * @return Query
     */
    public function from(string $table, ?string $alias = null): self {
        if ($alias) {
            $this->from[$table] = $alias;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }

    /**
     * @param \string[] ...$fields
     * @return Query
     */
    public function select(string ...$fields): self {
        $this->select = $fields;
        return $this;
    }

    /**
     * @param int $length
     * @param int $offset
     * @return Query
     */
    public function limit(int $length, int $offset = 0): self {
        $this->limit = "$offset, $length";
        return $this;
    }

    /**
     * @param string $order
     * @return Query
     */
    public function order(string  $order): self {
        $this->order[] = $order;
        return $this;
    }

    /**
     * @param string $table
     * @param string $condition
     * @param string $type
     * @return Query
     */
    public function join(string $table, string $condition, string $type = 'LEFT'): self {
        $this->joins[$type][] = [$table, $condition];
        return $this;
    }

    /**
     * @param \string[] ...$condition
     * @return Query
     */
    public function where(string ...$condition): self {
        $this->where = array_merge($this->where, $condition);
        return $this;
    }

    /**
     * @return int
     */
    public function count(): int {
        $query = clone $this;
        $table = current($this->from);
        return $query->select("COUNT($table.id)")->execute()->fetchColumn();
    }

    /**
     * @param array $params
     * @return Query
     */
    public function params(array $params): self {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * @param string $entity
     * @return Query
     */
    public function into(string $entity): self {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function fetch() {
        $record = $this->execute()->fetch(PDO::FETCH_ASSOC);
        if ($record === false) {
            return false;
        }
        if ($this->entity) {
            return Hydrator::hydrate($record, $this->entity);
        }
        return $record;
    }

    /**
     * @return QueryResult
     */
    public function fetchAll(): QueryResult {
        return new QueryResult($this->execute()->fetchAll(PDO::FETCH_ASSOC), $this->entity);

    }

    /**
     * @return mixed
     * @throws NoRecordException
     */
    public function fetchOrFail() {
        $record = $this->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function paginate(int $perPage, int $currentPage = 1): Pagerfanta {
        $paginator = new PaginateQuery($this);
        return (new Pagerfanta($paginator))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }

    /**
     * @return string
     */
    public function __toString(): string {
        $parts = ['SELECT'];
        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = '*';
        }
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if (!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = strtoupper($type) . " JOIN $table ON $condition";
                }
            }
        }
        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = "(" . join(') AND (',  $this->where) . ')';
        }
        if (!empty($this->order)) {
            $parts[] = 'ORDER BY';
            $parts[] = join(', ', $this->order);
        }
        if ($this->limit) {
            $parts[] = 'LIMIT ' . $this->limit;
        }
        return join(' ', $parts);
    }

    /**
     * @return string
     */
    private function buildFrom(): string {
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$key as $value";
            } else {
                $from[] = $value;
            }
        }
        return join(', ', $from);
    }

    /**
     * @return \PDOStatement
     */
    private function execute() {
        $query = $this->__toString();
        if (!empty($this->params)) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->pdo->query($query);
    }
}