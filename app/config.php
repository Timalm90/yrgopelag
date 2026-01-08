<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// Variables
$apiKey = $_ENV['API_KEY'] ?? null;
$mastercode = $_ENV['MASTERCODE'] ?? null;
