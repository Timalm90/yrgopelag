<section id="featureModal" style="display:none;">
    <article class="whiteBox modalContent">
        <span class="modalCloseFeature">&times;</span>

        <?php foreach ($categories as $category): ?>
            <div class="modalCategory" data-category-id="<?= $category['id']; ?>" style="display:none;">
                <h2><?= ucwords(htmlspecialchars($category['category'])); ?> Features</h2>
                <ul>
                    <?php
                    $features = getActiveFeaturesByCategory($pdo, $category['id']);
                    foreach ($features as $feature): ?>
                        <li>
                            <strong><?= ucwords(htmlspecialchars($feature['feature'])); ?>:</strong>
                            <?= htmlspecialchars($feature['description']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </article>
</section>