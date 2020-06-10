<?php

namespace App\Model;

abstract class Model
{
    protected $connection;

    public function __construct()
    {
        $this->connection = app()['connection'];
    }

    public function all()
    {
        return $this->connection
            ->query("SELECT * FROM {$this->table}")
            ->fetchAll();
    }

    public function insert($data)
    {
        $sql = $this->buildInsertQuery($data);
        $query = $this->connection->prepare($sql);
        $query->execute(array_values($data));
    }

    private function buildInsertQuery($data)
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

    private function getInsertToken($data)
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

    public function find($value, $by = 'id')
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$by} = ?";
        $query = $this->connection->prepare($sql);
        $query->execute([$value]);
        return $query->fetch();
    }

    public function update($data, $condition)
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

    private function getValues($data)
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

    private function getUpdateData($data, $condition)
    {
        $update_data = array_values($data);
        $update_data = array_merge($update_data, array_values($condition));
        return $update_data;
    }
}
