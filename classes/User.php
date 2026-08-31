<?php
declare(strict_types=1);

namespace classes;

use PDO;

require_once __DIR__ . '/../vendor/autoload.php';

class User
{
    private PDO $db;
    private int $id;
    private string $username;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConn();
    }

    public function register(string $username, string $password): RegistrationResult
    {
        if (empty($username) || empty($password)) {
            return new RegistrationResult(false, "All fields are required!");
        } else if (strlen($username) > 50) {
            return new RegistrationResult(false, "Username must be less than 50 characters!");
        } else if (strlen($password) > 255) {
            return new RegistrationResult(false, "Password must be less than 255 characters!");
        } else if ($this->is_username_existing($username)) {
            return new RegistrationResult(false, "Username already exists!");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password)");

        $success = $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword
        ]);

        if ($success) {
            return new RegistrationResult(true, "Registration successful!");
        }

        return new RegistrationResult(false, "Registration failed!");
    }

    private function is_username_existing(string $username): bool
    {
        $stmt = $this->db->prepare("SELECT username FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }
}