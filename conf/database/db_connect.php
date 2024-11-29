<?php
require_once __DIR__ . '../../../vendor/autoload.php'; // Load Composer autoload

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '../../../');
$dotenv->load();

// Use environment variables
$connect = mysqli_connect(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME'],
    $_ENV['DB_PORT']
);

if (!$connect) {
    die("Database connection error: " . mysqli_connect_error());
}
?>
