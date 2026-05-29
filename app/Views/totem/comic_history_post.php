<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
<div class="totem-shell">
    <?= view('totem/partials/topbar', ['nav' => $nav]) ?>
    <main class="content-area">
        <h1><?= lang('Totem.comic_history.post_title') ?></h1>
        <p>Post de historia: <?= $slug ?> (mock).</p>
    </main>
</div>
<?= $this->endSection() ?>
