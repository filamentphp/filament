<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env.testing file if it exists
// Using safeLoad() to avoid overriding environment variables that are already set
if (file_exists(__DIR__ . '/../.env.testing')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
    $dotenv->safeLoad();
}
