<?php

declare(strict_types=1);
require __DIR__ . "/../autoload.php";

$_SESSION['admin'] = NULL;
header("Location: /index.php"); // IN LOCALHOST
// header("Location: ../../index.php"); // IN DEPLOY
exit;
