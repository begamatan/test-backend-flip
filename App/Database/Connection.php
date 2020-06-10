<?php

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    public function start($config)
    {
        try {
            $conn = new PDO(
                "mysql:host={$config['host']};dbname={$config['db_name']}", $config['db_username'], $config['db_password']
            );
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage() . "" . PHP_EOL);
        }
    }
}
