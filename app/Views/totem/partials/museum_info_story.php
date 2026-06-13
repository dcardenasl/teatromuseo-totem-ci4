<?php
/**
 * Pantalla editorial de Explora el Museo.
 *
 * @param string $eyebrow
 * @param string $title
 * @param string $image
 * @param string $imageAlt
 * @param string $intro
 * @param array<int, array{title:string, copy:string}> $sections
 */

$sections = $sections ?? [];
?>

<div class="screen-page__body museum-info-story">
    <section class="museum-info-story__hero" aria-labelledby="museum-info-story-title">
        <p class="museum-info-story__eyebrow"><?= esc($eyebrow ?? '') ?></p>
        <h2 class="museum-info-story__title" id="museum-info-story-title"><?= safe_title($title ?? '') ?></h2>

        <figure class="museum-info-story__collage">
            <img src="<?= esc(base_url($image ?? ''), 'attr') ?>" alt="<?= esc($imageAlt ?? '', 'attr') ?>">
        </figure>
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
