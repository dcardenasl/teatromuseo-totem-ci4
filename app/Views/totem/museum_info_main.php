<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/menu_grid', [
            'items' => [
                [
                    'title' => lang('MuseumInfo.building'),
                    'href'  => base_url('museo/el-museo/edificio'),
                ],
                [
                    'title' => lang('MuseumInfo.institution'),
                    'href'  => base_url('museo/el-museo/institucion'),
                ],
                [
                    'title' => lang('MuseumInfo.today'),
                    'href'  => base_url('museo/el-museo/actualidad'),
                ],
            ],
            'showCoda' => false,
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('MuseumInfo.main_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
