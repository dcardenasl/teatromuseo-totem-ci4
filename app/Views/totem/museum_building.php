<?php
/**
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/museum_info_story', [
            'eyebrow' => lang('MuseumInfo.main_eyebrow'),
            'title' => lang('MuseumInfo.teatromuseo_history_title'),
            'image' => 'assets/img/museo/el-museo/collage-nuestra-historia.webp',
            'imageAlt' => lang('MuseumInfo.teatromuseo_history_alt'),
            'intro' => lang('MuseumInfo.main_copy'),
            'sections' => [
                [
                    'title' => lang('MuseumInfo.mission_title'),
                    'copy' => lang('MuseumInfo.mission_copy'),
                ],
                [
                    'title' => lang('MuseumInfo.vision_title'),
                    'copy' => lang('MuseumInfo.vision_copy'),
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
