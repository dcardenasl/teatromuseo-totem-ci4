<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.collection.clowns_title') ?></h1>
        <p>Catálogo de historia de payasos (mock).</p>
    </main>
</div>
<?= $this->endSection() ?>
