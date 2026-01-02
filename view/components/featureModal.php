<?php
// Fetch feature description
$features = getActiveFeatures($pdoBooking);
?>

<section id="featureModal">
    <article class=" whiteBox modalContent">
        <span class="modalClose" id="closeModal">&times;</span>
        <h2>Our features:</h2>
        <ul>
            <?php foreach ($features as $feature): ?>
                <li class="featureDescription">
                    <strong><?= ucwords(htmlspecialchars($feature['feature'])); ?>: </strong>
                    <?= htmlspecialchars($feature['description']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        </div>
        </div>