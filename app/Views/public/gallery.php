<section class="section">
    <div class="container">
        <div class="section-header">
            <h1 style="margin:0;">Gallery</h1>
        </div>

        <div class="filter-pills">
            <a href="<?= e(url('/gallery')) ?>" class="pill <?= $selectedCategory === '' ? 'is-active' : '' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= e(url('/gallery?category=' . urlencode((string) $cat))) ?>" class="pill <?= strcasecmp($selectedCategory, (string) $cat) === 0 ? 'is-active' : '' ?>">
                    <?= e($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($items)): ?>
            <div class="grid-3">
                <?php foreach (['Wood-Fired Hearth', 'Artisan Plates', 'Main Dining Room', 'Evening Pours', "Chef's Counter", 'Private Lounge'] as $tileTitle): ?>
                    <?php
                    $placeholderClass = 'tile';
                    $placeholderTag = $tileTitle;
                    $placeholderText = 'Photo coming soon';
                    require base_path('app/Views/partials/photo_placeholder.php');
                    ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($items as $item): ?>
                    <?php $itemType = (string) ($item['type'] ?? 'photo'); ?>
                    <article class="card">
                        <?php if ($itemType === 'youtube' && !empty($item['youtube_id'])): ?>
                            <?php $ytId = (string) $item['youtube_id']; ?>
                            <div class="media-frame">
                                <button
                                    type="button"
                                    class="youtube-facade"
                                    data-youtube-id="<?= e($ytId) ?>"
                                    aria-label="<?= e('Play video: ' . ($item['title'] ?: 'Renzo Gallery Video')) ?>"
                                >
                                    <img
                                        src="https://img.youtube.com/vi/<?= e($ytId) ?>/hqdefault.jpg"
                                        alt="<?= e($item['alt_text'] ?: ($item['title'] ?? 'Renzo YouTube video')) ?>"
                                        class="card-img"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                    <span class="yt-play-badge" aria-hidden="true">&#9654;</span>
                                </button>
                            </div>
                        <?php elseif (!empty($item['image_path'])): ?>
                            <?php $photoUrl = media_url($item['image_path']); ?>
                            <button
                                type="button"
                                class="gallery-photo-btn"
                                data-lightbox-trigger
                                data-lightbox-src="<?= e($photoUrl) ?>"
                                data-lightbox-title="<?= e($item['title'] ?? '') ?>"
                                data-lightbox-caption="<?= e($item['caption'] ?? '') ?>"
                                aria-label="<?= e('View photo: ' . ($item['title'] ?: 'Renzo Gallery Photo')) ?>"
                            >
                                <img
                                    src="<?= e($photoUrl) ?>"
                                    alt="<?= e($item['alt_text'] ?: ($item['title'] ?? 'Renzo photo')) ?>"
                                    class="card-img"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </button>
                        <?php else: ?>
                            <?php
                            $placeholderClass = '';
                            $placeholderTag = $item['category'] ?? 'Gallery';
                            $placeholderText = 'Photo coming soon';
                            require base_path('app/Views/partials/photo_placeholder.php');
                            ?>
                        <?php endif; ?>

                        <?php if (!empty($item['title']) || !empty($item['caption']) || !empty($item['category'])): ?>
                            <div class="card-body">
                                <?php if (!empty($item['category'])): ?>
                                    <span class="badge" style="margin-bottom:6px;"><?= e($item['category']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($item['title'])): ?>
                                    <h3 class="card-title"><?= e($item['title']) ?></h3>
                                <?php endif; ?>
                                <?php if (!empty($item['caption'])): ?>
                                    <p class="muted" style="margin:0;"><?= e($item['caption']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="lightbox" id="gallery-lightbox" role="dialog" aria-modal="true" aria-label="Image viewer" hidden>
    <div class="lightbox-backdrop" data-lightbox-close></div>
    <div class="lightbox-dialog">
        <button type="button" class="lightbox-close" data-lightbox-close aria-label="Close lightbox">&times;</button>
        <button type="button" class="lightbox-nav lightbox-prev" data-lightbox-prev aria-label="Previous photo">&lsaquo;</button>
        <figure class="lightbox-figure">
            <img src="" alt="" data-lightbox-img>
            <figcaption class="lightbox-caption">
                <strong data-lightbox-title-el></strong>
                <span data-lightbox-caption-el class="muted"></span>
            </figcaption>
        </figure>
        <button type="button" class="lightbox-nav lightbox-next" data-lightbox-next aria-label="Next photo">&rsaquo;</button>
    </div>
</div>
