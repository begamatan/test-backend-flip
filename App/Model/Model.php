<?php

namespace App\Model;

use PDO;

abstract class Model
{
    /**
     * PDO connection.
     *
     * @var PDO
     */
    protected $connection;

    public function __construct()
    {
        $this->connection = app()['connection'];
    }

    /**
     * Insert record to database.
     *
     * @param  array  $data  Data to insert
     * @return void
     */
    public function insert(array $data): void
    {
        $sql = $this->buildInsertQuery($data);
        $query = $this->connection->prepare($sql);
        $query->execute(array_values($data));
    }

    /**
     * Build sql query for insertion.
     *
     * @param  array  $data
     * @return string
     */
    private function buildInsertQuery(array $data): string
    {
        $key = implode(',', array_keys($data));
        $value = $this->getInsertToken($data);
        $sql = "INSERT INTO {$this->table} (
            {$key}
        ) VALUES (
            {$value}
        )";
        return $sql;
    }

    /**
     * Put placeholder for sql statement.
     *
     * @param  array  $data
     * @return string
     */
    private function getInsertToken(array $data): string
    {
        $token = '';
        for ($i = 0; $i < count($data); $i++) {
            $token .= '?';
            if ($i !== count($data) - 1) {
                $token .= ',';
            }
        }
        return $token;
    }

    /**
     * Find record in database.
     *
     * @param  string  $value
     * @param  string  $by
     * @return array
     */
    public function find(string $value, string $by = 'id'): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$by} = ?";
        $query = $this->connection->prepare($sql);
        $query->execute([$value]);
        $result = $query->fetch();
        return $result ? $result : [];
    }

    /**
     * Update record in database.
     *
     * @param  array  $data
     * @param  array  $condition
     * @return void
     */
    public function update(array $data, array $condition): void
    {
        // @todo update so condition can be multiple
        $values = $this->getValues($data);
        $where = $this->getValues($condition);
        $sql = "UPDATE {$this->table} SET {$values} WHERE {$where}";
        $query = $this->connection->prepare($sql);
        $query->execute(
            $this->getUpdateData($data, $condition)
        );
    }

    /**
     * Build string from array of data for SQL query.
     *
     * @param  array  $data
     * @return string
     */
    private function getValues(array $data): string
    {
        $values = "";
        $data = array_keys($data);
        for ($i = 0; $i < count($data); $i++) {
            $values .= "{$data[$i]}=?";
            if ($i !== count($data) - 1) {
                $values .= ",";
            }
        }
        return $values;
    }

    /**
     * Build array of data to replace placeholder in SQL query.
     *
     * @param  array  $data
     * @param  array  $condition
     * @return array
     */
    private function getUpdateData(array $data, array $condition): array
    {
        $update_data = array_values($data);
        $update_data = array_merge($update_data, array_values($condition));
        return $update_data;
    }
}
