<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.collection.main_title') ?></h1>
        <div class="menu-grid">
            <a href="<?= base_url('museo/coleccion/titeres') ?>" class="menu-card"><?= lang('Totem.collection.puppets') ?></a>
            <a href="<?= base_url('museo/coleccion/mascaras') ?>" class="menu-card"><?= lang('Totem.collection.masks') ?></a>
            <a href="<?= base_url('museo/coleccion/payasos') ?>" class="menu-card"><?= lang('Totem.collection.clowns') ?></a>
        </div>
    </main>
</div>
<?= $this->endSection() ?>
