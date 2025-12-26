<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";

// Log out logics

// In this file we logout users.

$_SESSION['admin'] = NULL;
header("Location: /toadtest/index.php");
