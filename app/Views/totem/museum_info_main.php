<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.museum_info.main_title') ?></h1>
        <div class="menu-grid">
            <a href="<?= base_url('museo/el-museo/edificio') ?>" class="menu-card"><?= lang('Totem.museum_info.building') ?></a>
            <a href="<?= base_url('museo/el-museo/institucion') ?>" class="menu-card"><?= lang('Totem.museum_info.institution') ?></a>
            <a href="<?= base_url('museo/el-museo/actualidad') ?>" class="menu-card"><?= lang('Totem.museum_info.today') ?></a>
        </div>
    </main>
</div>
<?= $this->endSection() ?>
