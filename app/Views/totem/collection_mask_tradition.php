<?php
/**
 * @var array $nav
 * @var array $story
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/museum_info_story', $story ?? []) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title'   => '',
        'content' => $content,
        'nav'     => $nav ?? [],
    ]) ?>
<?= $this->endSection() ?>
