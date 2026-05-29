<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.extension.title') ?></h1>
        <p>Formulario de contacto y teclado táctil (mock).</p>
    </main>
</div>
<?= $this->endSection() ?>
