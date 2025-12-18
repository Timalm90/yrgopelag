<?php

declare(strict_types=1);

// https://paragonie.com/blog/2015/06/preventing-xss-vulnerabilities-in-php-everything-you-need-know
// Instead of htmlspecialchars
function sanitizeString(string $input, string $encoding = 'UTF-8'): string
{
    return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
};

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
