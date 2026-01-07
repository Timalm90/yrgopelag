<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

// This file packs up content in .env and makes it usable in other files, this file should be required into other files to enable usage.


// Use this package
use Dotenv\Dotenv;

// Connect to local library & upload
$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// Save result to variable apiKey
$apiKey = $_ENV['API_KEY'] ?? null;
$mastercode = $_ENV['MASTERCODE'] ?? null;
