<?php
require_once __DIR__.'/../bootstrap/app.php';

use App\Utils\DB;

$db = DB::getDB();

// SQL statements to create schema
$sql = "";

try {
    $db->query($sql);
    echo "Database schema initialized successfully.\n";
} catch (PDOException $e) {
    die("Error initializing database schema: ".$e->getMessage());
}
