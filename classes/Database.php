<?php
declare(strict_types=1);

namespace classes;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $conn;

    private string $host = 'localhost';
    private string $dbname = 'phlog_db';
    private string $username = 'root';
    private string $password = '';

    public function __construct()
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConn()
    {
        return $this->conn;
    }
}