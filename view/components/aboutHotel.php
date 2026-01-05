<?php
// $owner = getSettingsValue($pdoBooking, "hotel_owner"); //
// $islandName = getSettingsValue($pdoBooking, "island_name"); //
// $hotelName = getSettingsValue($pdoBooking, "hotel_name"); //
// $numberOfStars = getSettingsValue($pdoBooking, "star_rating"); //
?>

<article class="dashboardBox">
    <h3>About hotel</h3>
    <p><strong>Island: </strong><?= htmlspecialchars($islandName) ?></p>
    <p><strong>Hotel: </strong><?= htmlspecialchars($hotelName) ?></p>
    <p><strong>Stars: </strong><?= htmlspecialchars($starRating) ?></p>
    <p><strong>Owner: </strong><?= htmlspecialchars($hotelOwner) ?></p>
</article>