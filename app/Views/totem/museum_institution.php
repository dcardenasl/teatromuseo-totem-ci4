<?php
/**
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/museum_info_story', [
            'eyebrow' => lang('MuseumInfo.church_eyebrow'),
            'title' => lang('MuseumInfo.church_history_title'),
            'image' => 'assets/img/museo/el-museo/collage-san-judas.webp',
            'imageAlt' => lang('MuseumInfo.church_history_alt'),
            'intro' => lang('MuseumInfo.church_history_intro'),
            'sections' => [
                [
                    'title' => lang('MuseumInfo.resistance_title'),
                    'copy' => lang('MuseumInfo.resistance_copy'),
                ],
            ],
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => '',
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
