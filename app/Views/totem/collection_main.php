<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="menu-grid">
            <?= view('totem/partials/card', [
                'title' => lang('Totem.collection.puppets'),
                'href'  => base_url('museo/coleccion/titeres'),
                'class' => ''
            ]) ?>
            <?= view('totem/partials/card', [
                'title' => lang('Totem.collection.masks'),
                'href'  => base_url('museo/coleccion/mascaras'),
                'class' => ''
            ]) ?>
            <?= view('totem/partials/card', [
                'title' => lang('Totem.collection.clowns'),
                'href'  => base_url('museo/coleccion/payasos'),
                'class' => ''
            ]) ?>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Totem.collection.main_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
