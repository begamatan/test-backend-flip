<?php
$config = require './config.php';
$db = $config['database'];

try {
    $conn = new PDO("mysql:host={$db['host']};dbname={$db['db_name']}", $db['db_username'], $db['db_password']);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE TABLE IF NOT EXISTS disbursement (
            id BIGINT AUTO_INCREMENT,
            transaction_id BIGINT NOT NULL,
            amount DECIMAL(13,4) NOT NULL,
            status VARCHAR(255) NOT NULL,
            timestamp DATETIME NOT NULL,
            bank_code VARCHAR(255) NOT NULL,
            account_number VARCHAR(255) NOT NULL,
            beneficiary_name VARCHAR(255) NOT NULL,
            remark VARCHAR(255) NOT NULL,
            receipt VARCHAR(255),
            time_served TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fee DECIMAL(13,4) NOT NULL,
            PRIMARY KEY (id)
        )";
    echo "Migrating table...\n";
    $conn->exec($sql);
    echo "Table migrated.\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
