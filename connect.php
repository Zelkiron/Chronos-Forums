<?php
try {
    $dsn = "mysql:host=localhost; dbname=forum";
    $pdo = new PDO($dsn, "root", "password");
} catch(PDOException $e) {
    die("Database connection failed. Contact the owner if this keeps happening.".$e->getMessage());
}
?>