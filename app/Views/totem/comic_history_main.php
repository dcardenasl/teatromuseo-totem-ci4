<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="menu-grid">
            <?php foreach (range(1, 3) as $i): ?>
                <?= view('totem/partials/card', [
                    'title' => "Capítulo Histórico #$i",
                    'href'  => '#',
                    'class' => ''
                ]) ?>
            <?php endforeach; ?>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('ComicHistory.main_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
