<?php
declare(strict_types=1);

namespace classes;

use PDO;
use PDOException;

require_once __DIR__ . '/../vendor/autoload.php';

class User
{
    private PDO $db;
    private Session $session;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConn();
        $this->session = Session::getInstance();
    }

    public function register(string $username, string $password): RegistrationResult
    {
        try {
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
                $userIdStmt = $this->db->prepare("SELECT id FROM users WHERE username = :username");
                $userIdStmt->execute([':username' => $username]);
                $userId = $userIdStmt->fetch(PDO::FETCH_ASSOC);

                $this->session->login((int) $userId['id'], $username);
                return new RegistrationResult(true, "Registration successful!");
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
        }

        return new RegistrationResult(false, "Registration failed!");
    }

    public function login(string $username, string $password): LoginResult {
        try {
            if (empty($username) || empty($password)) {
                return new LoginResult(false, "All fields are required!");
            } else if (strlen($username) > 50) {
                return new LoginResult(false, "Username must be less than 50 characters!");
            } else if (strlen($password) > 255) {
                return new LoginResult(false, "Password must be less than 255 characters!");
            } else if ($this->is_username_existing($username) && !$this->is_password_wrong($username, $password)) {
                $userIdStmt = $this->db->prepare("SELECT id FROM users WHERE username = :username");
                $userIdStmt->execute([':username' => $username]);
                $userId = $userIdStmt->fetch(PDO::FETCH_ASSOC);

                $this->session->login((int) $userId['id'], $username);

                return new LoginResult(true, "Login successful!");
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
        }

        return new LoginResult(false, "Wrong login info!");
    }

    private function is_username_existing(string $username): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        return (bool) $stmt->fetchColumn();
    }

    private function is_password_wrong(string $username, string $password): bool {
        $stmt = $this->db->prepare("SELECT username, password_hash FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return !password_verify($password, $result['password_hash']);
    }
}