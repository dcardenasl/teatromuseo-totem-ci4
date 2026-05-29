<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.collection.masks_title') ?></h1>
        <div class="menu-grid">
            <?php foreach ($traditions as $tradition): ?>
                <a href="<?= base_url('museo/coleccion/mascaras/' . $tradition['slug']) ?>" class="menu-card">
                    <h2><?= $tradition['title'] ?></h2>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
</div>
<?= $this->endSection() ?>
