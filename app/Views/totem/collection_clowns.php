<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <div class="menu-grid">
            <?php foreach (range(1, 4) as $i): ?>
                <?= view('totem/partials/card', [
                    'title' => "Payaso Histórico #$i",
                    'href'  => '#',
                    'class' => ''
                ]) ?>
            <?php endforeach; ?>
        </div>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.clowns_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
