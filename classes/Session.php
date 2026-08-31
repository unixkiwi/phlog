<?php
declare(strict_types=1);

namespace classes;

class Session
{
    private const SESSION_LIFETIME = 30 * 60;
    private const SESSION_DOMAIN = 'localhost';
    private const SESSION_PATH = '/';

    private static ?Session $instance = null;
    private bool $isLoggedIn = false;
    private bool $isConfigured = false;

    private function __construct()
    {
        if (!$this->isConfigured && session_status() === PHP_SESSION_NONE) {
            $this->configureSession();
            $this->isConfigured = true;
        }
        $this->startSession();
        $this->regenerateIfNeeded();
    }

    public static function getInstance(): Session
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function configureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_strict_mode', 1);

        session_set_cookie_params([
            'lifetime' => self::SESSION_LIFETIME,
            'domain' => self::SESSION_DOMAIN,
            'path' => self::SESSION_PATH,
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true
        ]);
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function regenerateIfNeeded(): void
    {
        $this->isLoggedIn = isset($_SESSION['user_id']);

        if ($this->isLoggedIn) {
            $this->regenerateLoggedInIfNeeded();
        } else {
            $this->regenerateGuestIfNeeded();
        }
    }

    private function regenerateLoggedInIfNeeded(): void
    {
        if (!$this->shouldRegenerate()) {
            return;
        }

        session_regenerate_id(true);

        $newSessionId = session_create_id();
        $sessionId = $newSessionId . '_' . $_SESSION['user_id'];
        session_id($sessionId);

        $this->resetTimer();
    }

    private function regenerateGuestIfNeeded(): void
    {
        if (!$this->shouldRegenerate()) {
            return;
        }

        session_regenerate_id(true);
        $this->resetTimer();
    }

    private function shouldRegenerate(): bool
    {
        if (!isset($_SESSION['last_regeneration'])) {
            return true;
        }

        return (time() - $_SESSION['last_regeneration']) >= self::SESSION_LIFETIME;
    }

    private function resetTimer(): void
    {
        $_SESSION['last_regeneration'] = time();
    }

    public function login(int $userId, string $username): void
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['logged_in'] = true;

        $this->regenerateLoggedInIfNeeded();
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        session_start();
        $this->resetTimer();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUsername(): ?string
    {
        return $_SESSION['username'] ?? null;
    }

    public function getLifetime(): int
    {
        return self::SESSION_LIFETIME;
    }
}