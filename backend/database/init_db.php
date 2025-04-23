<?php
// Database initialization script
require_once __DIR__ . '/../config/database.php';

function run_sql_file($conn, $file) {
    $sql = file_get_contents($file);
    if ($conn->multi_query($sql)) {
        do {
            // store first result set
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
        echo basename($file) . ": Success\n";
    } else {
        echo basename($file) . ": Error - " . $conn->error . "\n";
    }
}

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$files = [
    __DIR__ . '/users_table.sql',
    __DIR__ . '/media_table.sql',
    __DIR__ . '/ratings_table.sql',
    __DIR__ . '/analytics_table.sql',
    __DIR__ . '/admin_user.sql',
];

foreach ($files as $file) {
    run_sql_file($conn, $file);
}

$conn->close();
