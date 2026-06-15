<?php
/**
 * @var array $nav
 * @var string $eyebrow
 * @var string $title
 * @var string $copy
 * @var string $image
 * @var string $imageAlt
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="screen-page__body friends-page">
            <section class="friends-page__hero" aria-labelledby="friends-page-title">
                <p class="friends-page__eyebrow"><?= esc($eyebrow ?? '') ?></p>
                <h2 class="friends-page__title" id="friends-page-title"><?= esc($title ?? '') ?></h2>
                <p class="friends-page__copy"><?= esc($copy ?? '') ?></p>
            </section>

            <figure class="friends-page__art" aria-label="<?= esc($imageAlt ?? '', 'attr') ?>">
                <img src="<?= base_url($image ?? '') ?>" alt="<?= esc($imageAlt ?? '', 'attr') ?>">
            </figure>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => '',
        'content' => $content,
        'nav' => $nav ?? [],
    ]) ?>
<?= $this->endSection() ?>
