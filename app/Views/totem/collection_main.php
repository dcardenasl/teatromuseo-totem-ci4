<?php
/**
 * @var array<int, object> $sections Ítems de colección a renderear
 * @var array $nav                    Navegación shell
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
    <div class="collection-page">
        <?php foreach ($sections as $section): ?>
            <?= view('totem/partials/collection_band', ['item' => $section]) ?>
        <?php endforeach; ?>

        <div class="sr-only">
            <h2><?= esc(lang('Collection.main_title')) ?></h2>
            <p><?= esc(lang('Collection.heading_title')) ?></p>
        </div>
    </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.heading_title'),
        'content' => $content,
        'nav' => $nav ?? [],
        'titleWidth' => '10ch',
    ]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/collection-main.js') ?>"></script>
<?= $this->endSection() ?>
