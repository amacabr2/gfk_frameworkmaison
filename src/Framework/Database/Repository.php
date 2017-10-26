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
    protected $entity = \stdClass::class;

    /**
     * Repository constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function makeQuery(): Query {
        return (new Query($this->pdo))
            ->from($this->table, $this->table[0])
            ->into($this->entity);
    }

    /**
     * @return array
     */
    public function findList(): array {
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
     * @return Query
     */
    public function findAll(): Query {
        return $this->makeQuery();
    }

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value) {
        return $this->makeQuery()->where("$field = :field")->params(["field" => $value])->fetchOrfail();
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id) {
        return $this->makeQuery()->where("id = $id")->fetchOrfail();
    }

    /**
     * @return int
     */
    public function count(): int {
        return $this->makeQuery()->count();
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
     * @param array $params
     * @return string
     */
    private function buildFieldQuery(array $params) {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }


}