<?php
/**
 * @var array $nav
 */
?>
<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/museum_info_story', [
            'eyebrow' => lang('Extension.pending_eyebrow'),
            'title' => lang('Extension.title'),
            'hero' => [
                'frame' => 'assets/img/museo/el-museo/marco.webp',
                'items' => [
                    [
                        'type' => 'image',
                        'src' => 'assets/img/menu/menu_visitas.webp',
                        'alt' => lang('Extension.title'),
                    ],
                ],
            ],
            'intro' => lang('Extension.pending_intro'),
            'stats' => [
                ['label' => lang('Extension.pending_stat_state_label'), 'value' => lang('Extension.pending_stat_state_value')],
                ['label' => lang('Extension.pending_stat_focus_label'), 'value' => lang('Extension.pending_stat_focus_value')],
                ['label' => lang('Extension.pending_stat_route_label'), 'value' => lang('Extension.pending_stat_route_value')],
            ],
            'sections' => [
                [
                    'title' => lang('Extension.pending_section_title_1'),
                    'copy' => lang('Extension.pending_section_copy_1'),
                ],
                [
                    'title' => lang('Extension.pending_section_title_2'),
                    'copy' => lang('Extension.pending_section_copy_2'),
                ],
            ],
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Extension.title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
