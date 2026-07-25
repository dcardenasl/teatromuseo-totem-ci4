<?php
/**
 * @var string $eyebrow
 * @var string $title
 * @var string $subtitle
 * @var string $image
 * @var string $imageAlt
 * @var string $previousHref
 * @var string $nextHref
 */

$eyebrow = $eyebrow ?? '';
$title = $title ?? '';
$subtitle = $subtitle ?? '';
$image = $image ?? '';
$imageAlt = $imageAlt ?? $title;
$previousHref = $previousHref ?? '';
$nextHref = $nextHref ?? '';
?>

<section class="collection-detail-stage">
    <?php if ($eyebrow !== ''): ?>
        <p class="collection-detail-stage__eyebrow"><?= esc($eyebrow) ?></p>
    <?php endif; ?>

    <?php if ($title !== ''): ?>
        <h2 class="collection-detail-stage__title"><?= esc($title) ?></h2>
    <?php endif; ?>

    <?php if ($subtitle !== ''): ?>
        <p class="collection-detail-stage__subtitle"><?= esc($subtitle) ?></p>
    <?php endif; ?>

    <figure class="collection-detail-stage__media">
        <img
            class="collection-detail-stage__image"
            src="<?= esc(base_url($image), 'attr') ?>"
            alt="<?= esc($imageAlt, 'attr') ?>"
        >
    </figure>

    <div class="collection-detail-stage__controls" aria-label="<?= esc(lang('Collection.image_nav_label'), 'attr') ?>">
        <a class="collection-detail-stage__nav-btn" href="<?= esc(base_url($previousHref), 'attr') ?>" aria-label="<?= esc(lang('Collection.previous_detail'), 'attr') ?>">
            <img src="<?= esc(base_url('assets/img/ui/slider_left.webp'), 'attr') ?>" alt="" aria-hidden="true">
        </a>
        <a class="collection-detail-stage__nav-btn" href="<?= esc(base_url($nextHref), 'attr') ?>" aria-label="<?= esc(lang('Collection.next_detail'), 'attr') ?>">
            <img src="<?= esc(base_url('assets/img/ui/slider_right.webp'), 'attr') ?>" alt="" aria-hidden="true">
        </a>
    </div>
</section>
