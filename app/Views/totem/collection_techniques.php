<?php
/**
 * @var array<int, array<string, mixed>> $techniques
 * @var array<int, array{label:string, href:string, active?:bool, disabled?:bool}> $tabs
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/collection_grid', [
            'title'     => lang('Collection.techniques_title'),
            'intro'     => lang('Collection.techniques_intro'),
            'gridClass' => 'collection-grid--techniques',
            'items'     => $techniques ?? [],
            'tabs'      => $tabs ?? [],
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.techniques_title'),
        'content' => $content,
        'nav' => $nav ?? [],
    ]) ?>
<?= $this->endSection() ?>
