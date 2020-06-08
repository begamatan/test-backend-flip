<?php

namespace App\Model;

abstract class Model
{
    protected $connection;

    public function __construct()
    {
        $config = $this->getConfig()['database'];
        try {
            $this->connection = new \PDO("mysql:host={$config['host']};dbname={$config['db_name']}", $config['db_username'], $config['db_password']);
            // set the PDO error mode to exception
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage() . "\n";
        }
    }

    private function getConfig()
    {
        return include(__DIR__ . '/../../config.php');
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
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_values($data));
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
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetch();
    }

    public function update($data, $condition)
    {
        // @todo update so condition can be multiple
        $values = $this->getValues($data);
        $where = $this->getValues($condition);
        $sql = "UPDATE {$this->table} SET {$values} WHERE {$where}";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(
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
