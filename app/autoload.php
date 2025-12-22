<?php

declare(strict_types=1);

// JUST FOR DEVELOP ENVIRONMENT: Set cookie-lifetime to 0 (session dies when browser closes)
// --------------------- REMOVED BEFORE PRODUCTION!!!!! ---------------------
ini_set('session.cookie_lifetime', 0);

// Start the session:
session_start();

// Set time zone:
date_default_timezone_set('Europe/Stockholm');

// Encoding, extra security for making å, ä, ö  & emojis working
mb_internal_encoding('UTF-8');

// Requires
require __DIR__ . "/functions.php"; // Require functions
require __DIR__ . "/db.php"; // [SAMLA DB-ANROP]

// Require in config file, save to variable for later usage in files
$config = require __DIR__ . "/config.php";

// Connection to booking DB
$dbPathBooking = __DIR__ . "/database/bookings.sqlite3";
$pdoBooking = new PDO("sqlite:" . $dbPathBooking);

// Connection to admin DB
$dbPathAdmin = __DIR__ . "/database/admin.sqlite3";
$pdoAdmin = new PDO("sqlite:" . $dbPathAdmin);

// Guzzle client
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'https://www.yrgopelag.se']);
