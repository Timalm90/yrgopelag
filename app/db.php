<?php

declare(strict_types=1);

function checkAvailable(PDO $pdo, int $roomId): array
{
    $statement = $pdo->prepare("SELECT arrival, departure FROM bookings WHERE room_id = :room_id");
    $statement->bindParam(":room_id", $roomId, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getFeaturePrices(PDO $pdo): array
{
    $statement = $pdo->prepare("SELECT features.id, tiers.price_per_feature FROM features INNER JOIN tiers ON features.tier_id = tiers.id
    ");
    $statement->execute();
    $features = $statement->fetchAll(PDO::FETCH_ASSOC);

    $prices = [];
    foreach ($features as $feature) {
        $prices[(int)$feature['id']] = (int)$feature['price_per_feature'];
    }
    return $prices;
}

function getTierLevels(PDO $pdo): array
{
    $statement = $pdo->prepare("SELECT tier FROM tiers");
    $statement->execute();
    $tierLevels = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $tierLevels;
}

function getActiveFeatures(PDO $pdo): array
{
    $statement = $pdo->prepare("SELECT * FROM features WHERE is_active = 1");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getRooms(PDO $pdo): array
{
    $statement = $pdo->prepare("SELECT * FROM rooms");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
};

function getRoomPrices(PDO $pdo): array
{
    $statement = $pdo->query("SELECT id, price_per_night FROM rooms");
    $rooms = $statement->fetchAll(PDO::FETCH_ASSOC);
    $prices = [];
    foreach ($rooms as $room) {
        $prices[$room['id']] = (int)$room['price_per_night'];
    }
    return $prices;
}

function findGuest(PDO $pdo, string $name): int|NULL
{
    $statement = $pdo->prepare("SELECT id FROM guests WHERE name = :name");
    $statement->bindParam(":name", $name, PDO::PARAM_STR);
    $statement->execute();
    $guest = $statement->fetch(PDO::FETCH_ASSOC);
    return $guest['id'] ?? NULL;
};

function registerGuest(PDO $pdo, string $name): void
{
    $statement = $pdo->prepare("INSERT INTO guests (name) VALUES (:name)");
    $statement->bindParam(":name", $name, PDO::PARAM_STR);
    $statement->execute();
}

function roomBooking(PDO $pdo, int $guestId, int $roomId, DateTime $arrivalDT, DateTime $departureDT): void
{
    $statement = $pdo->prepare("INSERT INTO bookings (guest_id, room_id, arrival, departure) VALUES (:guest_id, :room_id, :arrival, :departure)");
    $statement->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $statement->bindParam(":room_id", $roomId, PDO::PARAM_INT);
    $statement->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $statement->bindParam(":departure", $departureDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $statement->execute();
}

function featureOnlyBooking(PDO $pdo, int $guestId, DateTime $arrivalDT): void
{
    $statement = $pdo->prepare("INSERT INTO bookings (guest_id, arrival) VALUES (:guest_id, :arrival)
    ");
    $statement->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $statement->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $statement->execute();
}

function findBookingId(PDO $pdo, int $guestId, DateTime $arrivalDT): int|NULL
{
    $statement = $pdo->prepare("SELECT id FROM bookings WHERE guest_id = :guest_id AND arrival = :arrival ORDER BY id DESC LIMIT 1");
    $statement->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $statement->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $statement->execute();
    $booking = $statement->fetch(PDO::FETCH_ASSOC);

    return $booking['id'] ?? null;
}

function registerFeatures(PDO $pdo, int $bookingId, array $featureIds): void
{
    $statement = $pdo->prepare("INSERT INTO booking_feature (booking_id, feature_id) VALUES (:booking_id, :feature_id)");
    foreach ($featureIds as $featureId) {
        $statement->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $statement->bindParam(':feature_id', $featureId, PDO::PARAM_INT);
        $statement->execute();
    }
}

// For Discounts
function getLuxuryRoomId(PDO $pdo): ?int
{
    $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room = 'luxury'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['id'] ?? null;
}
function getBowserFeatureId(PDO $pdo): ?int
{
    $stmt = $pdo->prepare("SELECT id FROM features WHERE feature = 'Bowser’s Castle Escape'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['id'] ?? null;
}
function checkLoyalCustomer(PDO $pdo, int $guestId): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS visits FROM bookings WHERE guest_id = :guestId");
    $stmt->bindParam(':guestId', $guestId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return ((int) $result['visits'] ?? 0) >= 1;
}
function getDiscount(PDO $pdo, string $type): int
{
    $stmt = $pdo->prepare("SELECT discount FROM discounts WHERE type = :type LIMIT 1");
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($row['discount']) ? (int)$row['discount'] : 0;
}

function getFeatureName(PDO $pdo, int $featureId): ?string
{
    $stmt = $pdo->prepare("SELECT feature FROM features WHERE id = :id");
    $stmt->bindParam(':id', $featureId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['feature'] ?? null;
}



// For Confirmation message in bookings
function getRoomName(PDO $pdo, int $roomId): ?string
{
    $stmt = $pdo->prepare("SELECT room FROM rooms WHERE id = :id");
    $stmt->bindParam(':id', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['room'] ?? null;
}


// Update prices
function updateRoomPrice(PDO $pdo, string $room, int $price): void
{
    $stmt = $pdo->prepare("UPDATE rooms SET price_per_night = :price WHERE room = :room");
    $stmt->bindParam(':price', $price, PDO::PARAM_INT);
    $stmt->bindParam(':room', $room, PDO::PARAM_STR);
    $stmt->execute();
}

function updateTierPrice(PDO $pdo, string $tierName, int $price): void
{
    $stmt = $pdo->prepare("UPDATE tiers SET price_per_feature = :price WHERE tier = :tierName");
    $stmt->bindParam(":price", $price, PDO::PARAM_INT);
    $stmt->bindParam(":tierName", $tierName, PDO::PARAM_STR);
}
