<?php
/**
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/menu_grid', [
            'items' => [
                [
                    'title' => lang('MuseumInfo.teatromuseo_history'),
                    'href'  => base_url('museo/el-museo/historia'),
                    'img'   => 'assets/img/museo/el-museo/collage-nuestra-historia.webp',
                ],
                [
                    'title' => lang('MuseumInfo.church_history'),
                    'href'  => base_url('museo/el-museo/iglesia'),
                    'img'   => 'assets/img/museo/el-museo/collage-san-judas.webp',
                ],
                [
                    'title' => lang('MuseumInfo.teatromuseo_today'),
                    'href'  => base_url('museo/el-museo/hoy'),
                    'img'   => 'assets/img/museo/el-museo/collage-historia-actual.webp',
                ],
            ],
            'gridClass' => 'menu-grid--museum-info',
            'showCoda' => false,
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Menu.el_museo'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
