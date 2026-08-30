<?php
declare(strict_types=1);

namespace classes;

readonly class LoginResult
{
    public function __construct(
        public bool   $success,
        public string $message = ""
    )
    {
    }
}