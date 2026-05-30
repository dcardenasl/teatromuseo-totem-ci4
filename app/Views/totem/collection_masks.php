<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="menu-grid">
            <?php foreach ($traditions as $tradition): ?>
                <?= view('totem/partials/card', [
                    'title' => $tradition['title'],
                    'href'  => base_url('museo/coleccion/mascaras/' . $tradition['slug']),
                    'class' => ''
                ]) ?>
            <?php endforeach; ?>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Totem.collection.masks_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
