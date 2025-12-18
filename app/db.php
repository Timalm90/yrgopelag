<?php

declare(strict_types=1);

function checkAvailable(PDO $pdo, int $roomId): array
{
    $statement = $pdo->prepare("SELECT arrival, departure FROM checkins WHERE room_id = :room_id");
    $statement->bindParam(":room_id", $roomId, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getFeaturePrices(PDO $pdo): array
{
    // [NOTE COST_PER_FEATURE - NEED TO CHANGE!!!]
    $statement = $pdo->prepare("SELECT features.id, tiers.cost_per_feature FROM features INNER JOIN tiers ON features.tier_id = tiers.id
    ");
    $statement->execute();
    $features = $statement->fetchAll(PDO::FETCH_ASSOC);

    $prices = [];
    foreach ($features as $feature) {
        $prices[(int)$feature['id']] = (int)$feature['cost_per_feature'];
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

function roomCheckin(PDO $pdo, int $guestId, int $roomId, DateTime $arrivalDT, DateTime $departureDT): void
{
    $statement = $pdo->prepare("
        INSERT INTO checkins (guest_id, room_id, arrival, departure)
        VALUES (:guest_id, :room_id, :arrival, :departure)
    ");
    $statement->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $statement->bindParam(":room_id", $roomId, PDO::PARAM_INT);
    $statement->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $statement->bindParam(":departure", $departureDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $statement->execute();
}

function featureOnlyCheckin(PDO $pdo, int $guestId, DateTime $arrivalDT): void
{
    $statement = $pdo->prepare("INSERT INTO checkins (guest_id, arrival) VALUES (:guest_id, :arrival)
    ");
    $statement->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $statement->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $statement->execute();
}

function findCheckinId(PDO $pdo, int $guestId, DateTime $arrivalDT): int|NULL
{
    $statement = $pdo->prepare("SELECT id FROM checkins WHERE guest_id = :guest_id AND arrival = :arrival ORDER BY id DESC LIMIT 1");
    $statement->bindParam(":guest_id", $guestId, PDO::PARAM_INT);
    $statement->bindParam(":arrival", $arrivalDT->format('Y-m-d H:i'), PDO::PARAM_STR);
    $statement->execute();
    $checkin = $statement->fetch(PDO::FETCH_ASSOC);

    return $checkin['id'] ?? null;
}

function registerFeatures(PDO $pdo, int $checkinId, array $featureIds): void
{
    $statement = $pdo->prepare("INSERT INTO checkin_feature (checkin_id, feature_id) VALUES (:checkin_id, :feature_id)");
    foreach ($featureIds as $featureId) {
        $statement->bindParam(':checkin_id', $checkinId, PDO::PARAM_INT);
        $statement->bindParam(':feature_id', $featureId, PDO::PARAM_INT);
        $statement->execute();
    }
}
