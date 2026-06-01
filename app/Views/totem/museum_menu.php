<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/menu_grid', [
            'items' => $items,
            'ariaLabel' => 'Secciones del museo',
            'showCoda' => false,
        ]) ?>

        <?= view('totem/partials/page_footer') ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Menu.explore_museum'),
        'titleWidth' => '9.5ch',
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
