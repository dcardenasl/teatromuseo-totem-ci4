<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <article>
            <h2><?= esc($technique['title']) ?></h2>
            <?php if (isset($technique['description'])): ?>
                <p><?= esc($technique['description']) ?></p>
            <?php endif; ?>
        </article>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.technique_prefix') . ' ' . $technique['title'],
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
