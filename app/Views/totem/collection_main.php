<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php
    $sections = [
        [
            'title' => lang('Collection.puppets'),
            'image' => base_url('assets/img/museum/cat_coleccion.webp'),
            'routeA' => [
                'label' => lang('Collection.collection_exhibit'),
                'href' => base_url('museo/coleccion/titeres/exhibicion'),
            ],
            'routeB' => [
                'label' => lang('Collection.collection_techniques'),
                'href' => base_url('museo/coleccion/titeres/tecnicas'),
            ],
            'bandClass' => 'collection-band--puppets',
        ],
        [
            'title' => lang('Collection.masks'),
            'image' => base_url('assets/img/museum/cat_el_museo.webp'),
            'routeA' => [
                'label' => lang('Collection.collection_exhibit'),
                'href' => base_url('museo/coleccion/mascaras/exhibicion'),
            ],
            'routeB' => [
                'label' => lang('Collection.collection_traditions'),
                'href' => base_url('museo/coleccion/mascaras/tradiciones'),
            ],
            'bandClass' => 'collection-band--masks',
        ],
        [
            'title' => lang('Collection.clowns'),
            'image' => base_url('assets/img/museum/cat_historia_comica.webp'),
            'routeA' => [
                'label' => lang('Collection.collection_exhibit'),
                'href' => null,
                'disabled' => true,
            ],
            'routeB' => [
                'label' => lang('Collection.collection_history'),
                'href' => base_url('museo/historia'),
            ],
            'bandClass' => 'collection-band--clowns',
        ],
    ];

    ob_start();
    ?>
    <div class="collection-page">
        <?php foreach ($sections as $section): ?>
            <?= view('totem/partials/collection_band', [
                'bandClass' => $section['bandClass'] ?? '',
                'item' => $section,
            ]) ?>
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
<script>
    // Mantiene esta pantalla anclada al inicio para reproducir el orden de lectura del PDF.
    window.history.scrollRestoration = 'manual';
    window.addEventListener('load', function () {
        window.scrollTo(0, 0);
    }, { once: true });
    window.addEventListener('pageshow', function () {
        window.scrollTo(0, 0);
    });
</script>
<?= $this->endSection() ?>
