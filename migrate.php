<?php
require_once './bootstrap.php';

try {
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
    echo "Migrating table..." . PHP_EOL;
    $query = app()['connection']->prepare($sql);
    $query->execute();
    echo "Table migrated." . PHP_EOL;
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
