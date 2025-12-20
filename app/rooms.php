<?php

declare(strict_types=1);
// require __DIR__ . "/autoload.php"; // Access to database, functions, DB-paths. Commented out, this lead to double sessions?

$roomPresentation = $pdo->prepare("SELECT * FROM rooms");
$roomPresentation->execute();
$rooms = $roomPresentation->fetchAll(PDO::FETCH_ASSOC);

// Cheatsheet:
// $roomPresentation['id'] = 1/2/3
// $roomPresentation['room'] = budget/standard/luxuary
// $roomPresentation['price_per_night'] = int/int/int
// $roomPresentation['title'] = int/int/int
// $roomPresentation['description'] = int/int/int