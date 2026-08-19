<?php
/**
 * Real billboard detail content — rendered both synchronously (cache hit)
 * and via `BillboardController::billboardDetailData()`'s async JSON
 * response (cache miss, TOTEM-BFF-17), so this markup only lives once.
 *
 * @var array{tags?:array, title:string, venue?:string, image?:string, ...} $detail
 * @var bool $stale
 */
$stale = $stale ?? false;
?>
<?php if ($stale): ?>
    <p class="content-stale-note"><?= esc(lang('Common.content_stale_note')) ?></p>
<?php endif; ?>
<section class="billboard-detail__intro" aria-label="<?= esc(lang('Billboard.editorial_intro_label'), 'attr') ?>">
    <div class="billboard-detail__tags">
        <?php foreach (($detail['tags'] ?? []) as $tag): ?>
            <span class="billboard-detail__tag chip"><?= esc($tag) ?></span>
        <?php endforeach; ?>
    </div>

    <h2 class="billboard-detail__show-title"><?= esc($detail['title'] ?? '') ?></h2>

    <?php if (!empty($detail['venue'])): ?>
    <p class="billboard-detail__meta">
        <span class="billboard-detail__meta-label"><?= esc(lang('Billboard.venue_label')) ?></span>
        <strong class="billboard-detail__meta-value"><?= esc($detail['venue']) ?></strong>
    </p>
    <?php endif; ?>
</section>

<section class="billboard-detail__media" aria-label="<?= esc(lang('Billboard.media_label'), 'attr') ?>">
    <figure class="billboard-detail__poster-frame" aria-label="<?= esc($detail['title'] ?? lang('Billboard.default_title'), 'attr') ?>" data-billboard-slider>
        <?php if (!empty($detail['images']) && is_array($detail['images'])): ?>
            <?php foreach ($detail['images'] as $index => $img): ?>
                <img
                    class="billboard-detail__poster"
                    src="<?= esc($img, 'attr') ?>"
                    alt="<?= esc($detail['title'] ?? lang('Billboard.default_title'), 'attr') ?>"
                    data-slide-index="<?= $index ?>"
                    style="display: <?= $index === 0 ? 'block' : 'none' ?>;"
                >
            <?php endforeach; ?>
        <?php else: ?>
            <img
                class="billboard-detail__poster"
                src="<?= esc(base_url('assets/img/menu/menu_programacion.webp'), 'attr') ?>"
                alt="<?= esc($detail['title'] ?? lang('Billboard.default_title'), 'attr') ?>"
                data-slide-index="0"
            >
        <?php endif; ?>
    </figure>

    <?php
    $hasMultiple = !empty($detail['images']) && is_array($detail['images']) && count($detail['images']) > 1;
    ?>
    <div class="billboard-detail__nav" aria-label="<?= esc(lang('Billboard.image_nav_label'), 'attr') ?>"<?= $hasMultiple ? '' : ' style="display: none;"' ?>>
        <button type="button" class="billboard-detail__nav-btn" data-slide-prev aria-label="<?= esc(lang('Billboard.previous_image'), 'attr') ?>">
            <img src="<?= esc(base_url('assets/img/ui/slider_left.webp'), 'attr') ?>" alt="" aria-hidden="true">
        </button>
        <button type="button" class="billboard-detail__nav-btn" data-slide-next aria-label="<?= esc(lang('Billboard.next_image'), 'attr') ?>">
            <img src="<?= esc(base_url('assets/img/ui/slider_right.webp'), 'attr') ?>" alt="" aria-hidden="true">
        </button>
    </div>
</section>

<section class="billboard-detail__body">
    <div class="billboard-detail__copy">
        <p class="billboard-detail__lead">
            <?= esc($detail['copy'] ?? '') ?>
        </p>
        <?php if (!empty($detail['secondaryCopy'])): ?>
            <p class="billboard-detail__lead billboard-detail__lead--secondary">
                <?= esc($detail['secondaryCopy']) ?>
            </p>
        <?php endif; ?>
    </div>

    <aside class="billboard-detail__sidebar" aria-label="<?= esc(lang('Billboard.quick_sheet_label'), 'attr') ?>">
        <div class="billboard-detail__schedule">
            <span class="billboard-detail__date"><?= esc($detail['date'] ?? '') ?></span>
            <span class="billboard-detail__time"><?= esc($detail['time'] ?? '') ?></span>
        </div>

    </aside>
</section>

<section class="billboard-detail__closing" aria-label="<?= esc(lang('Billboard.closing_label'), 'attr') ?>">
    <div class="billboard-detail__collage">
        <img
            class="billboard-detail__collage-image"
            src="<?= esc(base_url($detail['closingImage'] ?? 'assets/img/splash/collage-inicio.webp'), 'attr') ?>"
            alt=""
            aria-hidden="true"
        >
    </div>

    <div class="billboard-detail__contact">
        <img
            class="billboard-detail__qr-image"
            src="<?= esc(base_url($detail['qrImage'] ?? 'assets/img/school/teatroescuela-qr.webp'), 'attr') ?>"
            alt=""
            aria-hidden="true"
        >
        <p class="billboard-detail__contact-copy"><?= esc($detail['closingNote'] ?? lang('Billboard.default_closing_note')) ?></p>
    </div>

</section>
