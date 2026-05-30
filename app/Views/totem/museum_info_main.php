<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="menu-grid">
            <?= view('totem/partials/card', [
                'title' => lang('Totem.museum_info.building'),
                'href'  => base_url('museo/el-museo/edificio'),
                'class' => ''
            ]) ?>
            <?= view('totem/partials/card', [
                'title' => lang('Totem.museum_info.institution'),
                'href'  => base_url('museo/el-museo/institucion'),
                'class' => ''
            ]) ?>
            <?= view('totem/partials/card', [
                'title' => lang('Totem.museum_info.today'),
                'href'  => base_url('museo/el-museo/actualidad'),
                'class' => ''
            ]) ?>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Totem.museum_info.main_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
