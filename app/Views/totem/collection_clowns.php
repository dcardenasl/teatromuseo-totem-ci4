<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/menu_grid', [
            'items' => array_map(
                static fn (int $i): array => [
                    'title' => sprintf(lang('Collection.clown_placeholder'), $i),
                    'href' => null,
                    'disabled' => true,
                ],
                range(1, 4)
            ),
            'showCoda' => false,
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.clowns_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
