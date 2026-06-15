<?php
/**
 * @var array<int, array<string, mixed>> $items
 * @var array<int, array{label:string, href:string, active?:bool, disabled?:bool}> $tabs
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/collection_grid', [
            'title'     => lang('Collection.puppets_exhibit_title'),
            'intro'     => lang('Collection.puppets_exhibit_intro'),
            'gridClass' => 'collection-grid--exhibit',
            'items'     => $items ?? [],
            'tabs'      => $tabs ?? [],
            'footer'    => lang('Collection.exhibit_pager'),
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.puppets_exhibit_title'),
        'content' => $content,
        'nav' => $nav ?? [],
    ]) ?>
<?= $this->endSection() ?>
