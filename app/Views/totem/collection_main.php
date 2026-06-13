<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
    <div class="collection-page">
        <?php foreach ($sections as $section): ?>
            <?= view('totem/partials/collection_band', ['item' => $section]) ?>
        <?php endforeach; ?>

        <div class="sr-only">
            <h2><?= esc(lang('Collection.main_title')) ?></h2>
            <p><?= esc(lang('Collection.heading_title')) ?></p>
        </div>
    </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => '',
        'content' => $content,
        'nav' => $nav ?? [],
    ]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/collection-main.js') ?>"></script>
<?= $this->endSection() ?>
