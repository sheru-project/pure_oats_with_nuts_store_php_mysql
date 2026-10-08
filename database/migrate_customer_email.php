<?php
if(PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}

require_once __DIR__ . '/../config/db.php';

$column_result = $conn->query("SHOW COLUMNS FROM orders LIKE 'customer_email'");
if($column_result->num_rows === 0) {
    $conn->query('ALTER TABLE orders ADD COLUMN customer_email VARCHAR(254) NULL AFTER customer_name');
}

$index_result = $conn->query("SHOW INDEX FROM orders WHERE Key_name = 'idx_orders_customer_email'");
if($index_result->num_rows === 0) {
    $conn->query('CREATE INDEX idx_orders_customer_email ON orders (customer_email)');
}

fwrite(STDOUT, "Customer email column and index are ready.\n");
