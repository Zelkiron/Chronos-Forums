<?php
class ConnectToDatabase {
    private $dsn = "mysql:host=localhost; dbname=forum";
    private $username = "root";
    private $password = "password";
    public function connect() {
        $options = array(
            PDO::ATTR_PERSISTENT => true, 
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        $pdo = new PDO($this->dsn, $this->username, $this->password, $options);
        return $pdo;
    }
}
?>