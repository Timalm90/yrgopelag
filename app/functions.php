<?php

declare(strict_types=1);

// Booked dates in calendars
function bookedDays(array $bookings): array
{
    $days = [];

    foreach ($bookings as $b) {
        // DB returns string, convert to DateTime 
        $startDT = new DateTime($b['arrival']);
        $endDT   = new DateTime($b['departure']);

        // Extract day, convert to int
        $startDay = (int)$startDT->format('j');
        $endDay   = (int)$endDT->format('j');

        // Loop through all occupied dates, exclude checkout day
        for ($i = $startDay; $i < $endDay; $i++) {
            $days[] = $i;
        }
    }

    return $days;
}


// Count number of nights
function countNights(DateTime $arrival, DateTime $departure): int
{
    $arrivalDay   = (int)$arrival->format('j');
    $departureDay = (int)$departure->format('j');

    $nights = $departureDay - $arrivalDay;
    return $nights;
}


// Count total cost for hotel room
function countRoomCost(PDO $pdo, int $roomId, int $nights): int
{
    $roomPrices = getRoomPrices($pdo);
    return $roomPrices[$roomId] * $nights;
}


// Count total cost for features
function countFeatureCost(PDO $pdo, array $selectedFeatures): int
{
    if (empty($selectedFeatures)) return 0;
    $prices = getFeaturePrices($pdo);
    $total = 0;
    foreach ($selectedFeatures as $id) {
        $total += $prices[$id] ?? 0;
    }
    return $total;
}

function getErrorMessage(string $apiError): string
{
    return $apiError !== ''
        ? $apiError
        : 'Unknown error from central bank.';
}

function handleErrors(array $errors): void
{
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../index.php#errors");
        exit;
    }
}

// Prepare features for receipt. Returns an array with keys activity and tier.
function prepareFeaturesForReceipt(PDO $pdo, array $selectedFeatures): array
{
    $featuresUsed = [];

    // Fetch category name for ID 4 (hotel-specific)
    $stmt = $pdo->prepare("SELECT category FROM categories WHERE id = 4");
    $stmt->execute();
    $specificCategory = $stmt->fetch(PDO::FETCH_ASSOC)['category'];

    $statment = $pdo->prepare("SELECT categories.category, tiers.tier FROM features INNER JOIN categories ON features.category_id = categories.id INNER JOIN tiers ON features.tier_id = tiers.id WHERE features.id = :id");

    foreach ($selectedFeatures as $featureId) {
        $statment->bindParam(":id", $featureId, PDO::PARAM_INT);
        $statment->execute();
        $dbRow = $statment->fetch(PDO::FETCH_ASSOC);

        if (!$dbRow) continue;

        if ($dbRow['category'] === $specificCategory) {
            $dbRow['category'] = "hotel-specific";
        }

        $featuresUsed[] = [
            'activity' => $dbRow['category'],
            'tier'     => $dbRow['tier']
        ];
    }

    return $featuresUsed;
}
