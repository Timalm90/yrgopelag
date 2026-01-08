<?php

declare(strict_types=1);

function getSettingsValue(PDO $pdo, string $key): string
{
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE key = :key");
    $stmt->bindParam(":key", $key, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['value'];
}

function findAdmin(PDO $pdo, string $username): array
{
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username AND is_active = 1");
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function checkAvailable(PDO $pdo, int $roomId): array
{
    $stmt = $pdo->prepare("SELECT arrival, departure FROM bookings WHERE room_id = :room_id");
    $stmt->bindParam(":room_id", $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getFeaturePrices(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT features.id, tiers.price_per_feature FROM features INNER JOIN tiers ON features.tier_id = tiers.id
    ");
    $stmt->execute();
    $features = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $prices = [];
    foreach ($features as $feature) {
        $prices[(int)$feature['id']] = (int)$feature['price_per_feature'];
    }
    return $prices;
}

function getTierLevels(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT tier FROM tiers");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getActiveFeatures(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT * FROM features WHERE is_active = 1");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getRooms(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT * FROM rooms");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
};

function getRoomPrices(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT id, price_per_night FROM rooms");
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $prices = [];
    foreach ($rooms as $room) {
        $prices[$room['id']] = (int)$room['price_per_night'];
    }
    return $prices;
}

function getRoomPriceByName(PDO $pdo, string $room): ?int
{
    $stmt = $pdo->prepare("SELECT price_per_night FROM rooms WHERE room = :room LIMIT 1");
    $stmt->bindParam(':room', $room, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['price_per_night'] ?? null;
}

function getTierPriceByName(PDO $pdo, string $tier): ?int
{
    $stmt = $pdo->prepare("SELECT price_per_feature FROM tiers WHERE tier = :tier LIMIT 1");
    $stmt->bindParam(':tier', $tier, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['price_per_feature'] ?? null;
}

function findGuest(PDO $pdo, string $name): ?int
{
    $stmt = $pdo->prepare("SELECT id FROM guests WHERE name = :name");
    $stmt->bindParam(":name", $name, PDO::PARAM_STR);
    $stmt->execute();
    $guest = $stmt->fetch(PDO::FETCH_ASSOC);
    return $guest['id'] ?? NULL;
};

function registerGuest(PDO $pdo, string $name): void
{
    $name = strtolower($name);
    $stmt = $pdo->prepare("INSERT INTO guests (name) VALUES (:name)");
    $stmt->bindParam(":name", $name, PDO::PARAM_STR);
    $stmt->execute();
}

function roomBooking(PDO $pdo, int $guestId, int $roomId, DateTime $arrivalDT, DateTime $departureDT): void
{
    $stmt = $pdo->prepare("INSERT INTO bookings (guest_id, room_id, arrival, departure) VALUES (:guest_id, :room_id, :arrival, :departure)");
    $stmt->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $stmt->bindParam(":room_id", $roomId, PDO::PARAM_INT);
    $stmt->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $stmt->bindParam(":departure", $departureDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $stmt->execute();
}

function daypassBooking(PDO $pdo, int $guestId, DateTime $arrivalDT): void
{
    $stmt = $pdo->prepare("INSERT INTO bookings (guest_id, arrival) VALUES (:guest_id, :arrival)
    ");
    $stmt->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $stmt->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $stmt->execute();
}

function findBookingId(PDO $pdo, int $guestId, DateTime $arrivalDT): int|NULL
{
    $stmt = $pdo->prepare("SELECT id FROM bookings WHERE guest_id = :guest_id AND arrival = :arrival ORDER BY id DESC LIMIT 1");
    $stmt->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $stmt->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['id'] ?? null;
}

function registerFeatures(PDO $pdo, int $bookingId, array $featureIds): void
{
    $stmt = $pdo->prepare("INSERT INTO booking_feature (booking_id, feature_id) VALUES (:booking_id, :feature_id)");
    foreach ($featureIds as $featureId) {
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->bindParam(':feature_id', $featureId, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// For Discounts
function getLuxuryRoomId(PDO $pdo): ?int
{
    $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room = 'luxury'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['id'] ?? null;
}

function getBowserFeatureId(PDO $pdo): ?int
{
    $stmt = $pdo->prepare("SELECT id FROM features WHERE feature = 'Bowser’s Castle Escape'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['id'] ?? null;
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
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['discount']) ? (int)$result['discount'] : 0;
}

function getDiscountInfo(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT * FROM discounts");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getFeatureName(PDO $pdo, int $featureId): ?string
{
    $stmt = $pdo->prepare("SELECT feature FROM features WHERE id = :id");
    $stmt->bindParam(':id', $featureId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['feature'] ?? null;
}

// For Confirmation message in bookings
function getRoomName(PDO $pdo, int $roomId): ?string
{
    $stmt = $pdo->prepare("SELECT room FROM rooms WHERE id = :id");
    $stmt->bindParam(':id', $roomId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['room'] ?? null;
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
    $stmt->execute();
}

// Buy/Add features
function findNonActiveFeatures(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT features.id, features.feature, features.is_active, categories.category, tiers.tier, tiers.cost_per_tier FROM features INNER JOIN categories ON features.category_id = categories.id INNER JOIN tiers ON features.tier_id = tiers.id
    WHERE features.is_active = 0");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
};

function activateFeature(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare("UPDATE features SET is_active = 1 WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
}

//Statistics
function topFeatures(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT booking_feature.feature_id, features.feature, COUNT (feature_id) AS counts FROM booking_feature INNER JOIN features ON features.id = feature_id GROUP BY feature_id ORDER BY counts DESC LIMIT 5");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function countDayPass(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS daypass FROM bookings WHERE room_id IS NULL");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function countRoomBookings(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS booked FROM bookings WHERE room_id IS NOT NULL");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}
