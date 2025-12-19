<?php

declare(strict_types=1);
ob_start();

require __DIR__ . "/autoload.php";

// ------------------ FETCH FORM DATA ------------------
$bookingType = $_POST['bookingType'] ?? 'room';
$selectedRoomId = isset($_POST['room']) ? (int)$_POST['room'] : null;
$arrivalInput = $_POST['arrivalDate'] ?? null;
$departureInput = $_POST['departureDate'] ?? null;
$selectedFeatures = $_POST['features'] ?? [];

$totalRoomCost = 0;
$totalFeatureCost = 0;
$errors = [];

// ------------------ HELPER FUNCTIONS ------------------

function countNightsFE(DateTime $arrival, DateTime $departure): int
{
    $arrivalDay   = (int)$arrival->format('j');
    $departureDay = (int)$departure->format('j');

    return $departureDay - $arrivalDay;
}

function getRoomPricesFE(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT id, price_per_night FROM rooms");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $prices = [];
    foreach ($rooms as $room) {
        $prices[$room['id']] = (int)$room['price_per_night'];
    }
    return $prices;
}

function countRoomCostFE(PDO $pdo, int $roomId, int $nights): int
{
    $roomPrices = getRoomPrices($pdo);
    return ($roomPrices[$roomId] ?? 0) * $nights;
}

// ------------------ ROOM COST ------------------
if ($bookingType === 'room' && $selectedRoomId && $arrivalInput && $departureInput) {
    try {
        $arrivalDT = new DateTime($arrivalInput . ' 15:00');
        $departureDT = new DateTime($departureInput . ' 11:00');

        $nights = countNights($arrivalDT, $departureDT);
        if ($nights < 0) {
            $errors[] = "Check your dates for arrival and departure.";
            $nights = 0;
        }

        $totalRoomCost = countRoomCost($pdo, $selectedRoomId, $nights);
    } catch (Exception $e) {
        $errors[] = "Invalid date format.";
    }
}

// ------------------ FEATURES ------------------
if (!empty($selectedFeatures)) {
    $placeholders = implode(',', array_fill(0, count($selectedFeatures), '?'));
    $stmt = $pdo->prepare("
        SELECT f.id, t.cost_per_feature
        FROM features f
        INNER JOIN tiers t ON f.tier_id = t.id
        WHERE f.id IN ($placeholders)
    ");
    $stmt->execute(array_map('intval', $selectedFeatures));
    $features = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($features as $feature) {
        $totalFeatureCost += (int)$feature['cost_per_feature'];
    }
}

// ------------------ TOTAL PRICE ------------------
$totalPrice = $totalRoomCost + $totalFeatureCost;

// ------------------ RETURN JSON ------------------
ob_end_clean();
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'totalPrice' => $totalPrice,
    'errors' => $errors
]);
