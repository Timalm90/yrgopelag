<?php
// Fetch feature description
// $activeFeatures = getActiveFeatures($pdoBooking);
?>

<section id="featureModal">
    <article class=" whiteBox modalContent">
        <span class="modalCloseFeature" id="closeModalFeature">&times;</span>
        <h2>Our features:</h2>
        <ul>
            <?php foreach ($activeFeatures as $feature): ?>
                <li class="featureDescription">
                    <strong><?= ucwords(htmlspecialchars($feature['feature'])); ?>: </strong>
                    <?= htmlspecialchars($feature['description']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </article>
</section>