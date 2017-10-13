<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 13/10/17
 * Time: 15:10
 */

namespace Framework\Database;


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
     * @var \PDO
     */
    private $pdo;

    /**
     * @var array
     */
    private $params;

    /**
     * Query constructor.
     * @param \PDO $pdo
     */
    public function __construct(?\PDO $pdo = null){
        $this->pdo = $pdo;
    }

    /**
     * @param string $table
     * @param string $alias
     * @return Query
     */
    public function from(string $table, ?string $alias = null): self {
        if ($alias) {
            $this->from[$alias] = $table;
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
        $this->select("COUNT(id)");
        return $this->execute()->fetchColumn();
    }

    /**
     * @param array $params
     * @return Query
     */
    public function params(array $params): self {
        $this->params = $params;
        return $this;
    }

    /**
     * @return string
     */
    function __toString(): string {
        $parts = ['SELECT'];
        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = '*';
        }
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = "(" . join(') AND (',  $this->where) . ')';
        }
        return join(' ', $parts);
    }

    private function buildFrom(): string {
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$value as $key";
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
        if ($this->params) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->pdo->query($query);
    }

}