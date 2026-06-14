<?php
/**
 * @var array{visualClass:string, detailsTitle:string, detailsCopy:string, title:string, stats?:array<int, array{label:string, value:string}>} $section
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body">
            <section class="content-grid content-grid--section">
                <div class="<?= esc($section['visualClass']) ?>" aria-hidden="true"></div>

                <article class="content-panel content-panel--soft">
                    <h2 class="content-panel__title"><?= esc($section['detailsTitle']) ?></h2>
                    <p class="content-panel__text"><?= esc($section['detailsCopy']) ?></p>

                    <?php if (!empty($section['stats'])): ?>
                        <div class="stat-grid stat-grid--compact">
                            <?php foreach ($section['stats'] as $stat): ?>
                                <div class="stat-card">
                                    <span class="stat-card__label"><?= esc($stat['label']) ?></span>
                                    <span class="stat-card__value"><?= esc($stat['value']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            </section>
        </div>

    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => esc($section['title']),
        'content' => $content,
        'nav' => $nav ?? [],
        'footerVariant' => 'section',
    ]) ?>
<?= $this->endSection() ?>
