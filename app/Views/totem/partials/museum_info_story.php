<?php
/**
 * Pantalla compartida para relatos del museo.
 *
 * @param string $eyebrow
 * @param string $title
 * @param string $intro
 * @param array<int, array{title:string, copy:string}> $sections
 * @param array{frame?:string, items?:array<int, array{type?:string, src?:string, alt?:string}>} $hero
 * @param string $image
 * @param string $imageAlt
 * @param bool $showTitle
 */

$hero = $hero ?? [];
$sections = $sections ?? [];
$heroItems = $hero['items'] ?? [];
$frame = $hero['frame'] ?? 'assets/img/museo/el-museo/marco.webp';
$heroItem = is_array($heroItems) && isset($heroItems[0]) && is_array($heroItems[0]) ? $heroItems[0] : null;
$showTitle = $showTitle ?? true;
?>

<div class="screen-page__body museum-info-story">
    <section class="museum-info-story__hero"<?= $showTitle ? ' aria-labelledby="museum-info-story-title"' : ' aria-label="' . esc($title ?? '', 'attr') . '"' ?>>
        <p class="museum-info-story__eyebrow"><?= esc($eyebrow ?? '') ?></p>
        <?php if ($showTitle): ?>
        <h2 class="museum-info-story__title" id="museum-info-story-title"><?= safe_title($title ?? '') ?></h2>
        <?php endif; ?>

        <?php if ($heroItem !== null): ?>
            <figure class="museum-info-story__collage">
                <img src="<?= esc(base_url($heroItem['src'] ?? ''), 'attr') ?>" alt="<?= esc($heroItem['alt'] ?? '', 'attr') ?>">
            </figure>
        <?php else: ?>
            <figure class="museum-info-story__collage">
                <img src="<?= esc(base_url($image ?? ''), 'attr') ?>" alt="<?= esc($imageAlt ?? '', 'attr') ?>">
            </figure>
        <?php endif; ?>
    </section>

    <section class="museum-info-story__content" aria-label="<?= esc($title ?? '', 'attr') ?>">
        <p class="museum-info-story__intro"><?= esc($intro ?? '') ?></p>

        <?php foreach ($sections as $section): ?>
            <article class="museum-info-story__section">
                <h3 class="museum-info-story__section-title"><?= esc($section['title'] ?? '') ?></h3>
                <p class="museum-info-story__section-copy"><?= esc($section['copy'] ?? '') ?></p>
            </article>
        <?php endforeach; ?>
    </section>
</div>
