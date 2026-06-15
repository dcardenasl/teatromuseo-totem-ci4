<?php
/**
 * @var array{visualClass:string, detailsTitle:string, detailsCopy:string, title:string} $section
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
