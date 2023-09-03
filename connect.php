<?php
$dsn = "mysql:host=localhost; dbname=forum";
$pdo = new PDO($dsn, "root", "password");
if(!($pdo)) {
    die('Database connection failed. Contact the owner if this keeps happening: '.$pdo->ErrorInfo);
}
?>