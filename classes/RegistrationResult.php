<?php
declare(strict_types=1);

namespace classes;

readonly class RegistrationResult
{
    public function __construct(
        public bool   $success,
        public string $message = ""
    )
    {
    }
}