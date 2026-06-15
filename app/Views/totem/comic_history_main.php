<?php
/**
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="collection-page collection-page--history">
            <header class="collection-heading">
                <p class="collection-heading__eyebrow"><?= esc(lang('ComicHistory.details_title')) ?></p>
                <p class="collection-heading__copy"><?= esc(lang('ComicHistory.main_copy')) ?></p>
            </header>

            <?= view('totem/partials/menu_grid', [
                'items' => [
                    [
                        'title' => lang('ComicHistory.entry_circus_history'),
                        'href' => base_url('museo/historia/historia-del-circo'),
                        'img' => 'assets/img/museo/historia/collage-circo.webp',
                    ],
                    [
                        'title' => lang('ComicHistory.entry_clowns_history'),
                        'href' => base_url('museo/historia/historia-de-los-payasos'),
                        'img' => 'assets/img/museo/historia/collage-teatro.webp',
                    ],
                ],
                'gridClass' => 'menu-grid--history',
                'showCoda' => false,
            ]) ?>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('ComicHistory.main_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
