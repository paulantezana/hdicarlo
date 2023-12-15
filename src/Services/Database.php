<?php

class Database
{
    private $connection;
    public function __construct()
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $this->connection = new PDO('mysql:host=' . APP_ENV['DB_HOST'] . ';dbname=' . APP_ENV['DB_NAME'], APP_ENV['DB_USER'], APP_ENV['DB_PASSWORD'], $options);
        $this->connection->exec("SET CHARACTER SET UTF8");
    }
    public function getConnection()
    {
        return $this->connection;
    }
    public function closeConnection()
    {
        $this->connection = null;
    }
}
