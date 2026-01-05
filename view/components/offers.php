<div class="stickyOffers">
    <?php foreach ($showOffers as $offer): ?>
        <p><strong><?= ucwords(htmlspecialchars($offer['type'])) ?>: </strong> <?= htmlspecialchars($offer['description']) ?></p>
    <?php endforeach ?>
</div>