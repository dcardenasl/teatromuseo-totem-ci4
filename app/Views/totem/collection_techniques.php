<?php
/**
 * @var array<int, array{title:string, slug:string}> $techniques
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/menu_grid', [
                'items' => array_map(
                    static fn (array $technique): array => [
                        'title' => $technique['title'],
                        'href'  => base_url('museo/coleccion/titeres/tecnicas/' . $technique['slug']),
                    ],
                    $techniques
                ),
            'showCoda' => false,
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.techniques_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
