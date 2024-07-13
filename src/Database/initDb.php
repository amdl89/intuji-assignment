<?php
require_once __DIR__.'/../../bootstrap/app.php';

use App\Database\DBFactory;

$db = DBFactory::getDB();

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sid TEXT NOT NULL,
    email TEXT NOT NULL,
    name TEXT NOT NULL,
    picture TEXT NOT NULL,
    calendar_access_token TEXT DEFAULT NULL,
    calendar_refresh_token TEXT DEFAULT NULL,
    UNIQUE(sid)
    UNIQUE(email)
);";

try {
    $db->query($sql);
    echo "Database schema initialized successfully.\n";
} catch (PDOException $e) {
    die("Error initializing database schema: ".$e->getMessage());
}
