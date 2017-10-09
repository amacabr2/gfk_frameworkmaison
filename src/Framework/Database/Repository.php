<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 04/10/17
 * Time: 10:47
 */

namespace Framework\Database;


use Pagerfanta\Pagerfanta;
use PDO;

class Repository {

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string|null
     */
    protected $entity;

    /**
     * Repository constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta {
        $query = new PaginateQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * @return array
     */
    public function findList():array {
        $list = [];
        $results = $this->pdo
            ->query("SELECT * FROM $this->table")
            ->fetchAll(PDO::FETCH_NUM);
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * @return array
     */
    public function findAll(): array {
        $statement = $this->pdo->query("SELECT * FROM $this->table");
        if ($this->entity) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        }
        return $statement->fetchAll();
    }

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value) {
        return $this->fetchOrFail("SELECT * FROM $this->table WHERE $field = ?", [$value]);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id) {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    /**
     * @return int
     */
    public function count(): int {
        return $this->fetchColumn("SELECT COUNT(id) FROM {$this->table}");
    }

    /**
     * @param $params
     * @return bool
     */
    public function insert($params): bool {
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields);
            $statement = $this->pdo->prepare("INSERT INTO {$this->table} ({$fields}) VALUES ({$values})");
        return $statement->execute($params);
    }

    /**
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool {
        $fieldsQuery = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $statement = $this->pdo->prepare("UPDATE $this->table SET $fieldsQuery WHERE id = :id");
        return $statement->execute($params);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $statement->execute(['id' => $id]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool {
        $statement = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }

    /**
     * @return string
     */
    public function getTable(): string {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getEntity(): string {
        return $this->entity;
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO {
        return $this->pdo;
    }

    /**
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = []) {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        if ($this->entity) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        }
        $record =  $statement->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * @param string $query
     * @param array $params
     * @return mixed
     */
    protected function fetchColumn(string $query, array $params = []) {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        if ($this->entity) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        return $statement->fetchColumn();
    }

    /**
     * @return string
     */
    protected function paginationQuery() {
        return "SELECT * FROM {$this->table}";
    }

    /**
     * @param array $params
     * @return string
     */
    private function buildFieldQuery(array $params) {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }


}