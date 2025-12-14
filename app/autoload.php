<?php

declare(strict_types=1);

// Start the session:
session_start();

// Set time zone:
date_default_timezone_set('Europe/Stockholm');

// Encoding, extra security for making å, ä, ö  & emojis working
mb_internal_encoding('UTF-8');

// Require in functions file
require __DIR__ . "/functions.php";

// Require in config file, save to variable for later usage in files
$config = require __DIR__ . "/config.php";

// Connection to DB
$dbPath = __DIR__ . "/database/filename.db";
$pdo = new PDO("sqlite:" . $dbPath);
