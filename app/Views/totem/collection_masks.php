<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php ob_start(); ?>
        <?= view('totem/partials/menu_grid', [
            'items' => array_map(
                static fn (array $tradition): array => [
                    'title' => $tradition['title'],
                    'href'  => base_url('museo/coleccion/mascaras/' . $tradition['slug']),
                ],
                $traditions
            ),
            'showCoda' => false,
        ]) ?>
    <?php $content = ob_get_clean(); ?>

    <?= view('totem/partials/page_shell', [
        'title' => lang('Collection.masks_title'),
        'content' => $content,
        'nav' => $nav ?? []
    ]) ?>
<?= $this->endSection() ?>
