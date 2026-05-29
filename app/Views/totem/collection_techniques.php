<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.collection.techniques_title') ?></h1>
        <div class="menu-grid">
            <?php foreach ($techniques as $technique): ?>
                <a href="<?= base_url('museo/coleccion/titeres/' . $technique['slug']) ?>" class="menu-card">
                    <h2><?= $technique['title'] ?></h2>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
</div>
<?= $this->endSection() ?>
