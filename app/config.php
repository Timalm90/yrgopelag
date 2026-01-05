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


// ------------------------------------- LOG -------------------------------------
// Extra code lines from example/lesson, unnecessary? 
// header("Content-type: application/json");
// echo json_encode(['api_key' => $apiKey]);
// var_dump(json_encode(['api_key' => $apiKey]));

//2025-12-13: Checked if connected correctly. Result: YES!
// var_dump($apiKey);
// var_dump($adminuser);
// var_dump($password);

//2025-12-13: These variables should be ready to use now :)