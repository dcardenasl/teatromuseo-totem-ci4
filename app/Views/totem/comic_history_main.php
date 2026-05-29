<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.comic_history.main_title') ?></h1>
        <div class="timeline-container">
            <!-- Placeholder for timeline -->
            <p>Timeline principal en construcción</p>
        </div>
    </main>
</div>
<?= $this->endSection() ?>
