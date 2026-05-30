<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="menu-grid">
            <?php foreach ($techniques as $technique): ?>
                <a href="<?= base_url('museo/coleccion/titeres/' . $technique['slug']) ?>" class="menu-card">
                    <h2><?= $technique['title'] ?></h2>
                </a>
            <?php endforeach; ?>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Totem.collection.techniques_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
