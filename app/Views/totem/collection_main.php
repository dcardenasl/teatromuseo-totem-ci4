<?= $this->extend('layouts/MainLayout') ?>

<?= $this->section('content') ?>
    <?php
    $sections = [
        [
            'title' => lang('Collection.puppets'),
            'image' => base_url('assets/img/museum/cat_coleccion.webp'),
            'routeA' => [
                'label' => lang('Collection.collection_exhibit'),
                'href' => base_url('museo/coleccion/titeres'),
            ],
            'routeB' => [
                'label' => lang('Collection.collection_techniques'),
                'href' => base_url('museo/coleccion/titeres'),
            ],
            'bandClass' => 'collection-band--puppets',
        ],
        [
            'title' => lang('Collection.clowns'),
            'image' => base_url('assets/img/museum/cat_historia_comica.webp'),
            'routeA' => [
                'label' => lang('Collection.collection_history'),
                'href' => base_url('museo/coleccion/payasos'),
            ],
            'routeB' => [
                'label' => lang('Collection.collection_clown_theatre'),
                'href' => base_url('museo/coleccion/payasos'),
            ],
            'bandClass' => 'collection-band--clowns',
        ],
        [
            'title' => lang('Collection.masks'),
            'image' => base_url('assets/img/museum/cat_el_museo.webp'),
            'routeA' => [
                'label' => lang('Collection.collection_exhibit'),
                'href' => base_url('museo/coleccion/mascaras'),
            ],
            'routeB' => [
                'label' => lang('Collection.collection_traditions'),
                'href' => base_url('museo/coleccion/mascaras'),
            ],
            'bandClass' => 'collection-band--masks',
        ],
    ];

    ob_start();
    ?>
    <div class="collection-page">
        <header class="collection-heading">
            <h2 class="collection-heading__title"><?= esc(lang('Collection.heading_title')) ?></h2>
        </header>

        <?php foreach ($sections as $section): ?>
            <?= view('totem/partials/collection_band', [
                'bandClass' => $section['bandClass'] ?? '',
                'item' => $section,
            ]) ?>
        <?php endforeach; ?>

        <footer class="collection-footer" aria-hidden="true">
            <div class="collection-footer__logo collection-footer__logo--state">
                <img
                    src="<?= esc(base_url('assets/img/logos/ministerio_culturas_chile.png')) ?>"
                    alt="Ministerio de las Culturas, las Artes y el Patrimonio de Chile"
                    loading="lazy"
                >
            </div>
            <div class="collection-footer__logo collection-footer__logo--brand">
                <span class="collection-footer__brand-mark"></span>
                <span>Teatromuseo</span>
            </div>
        </footer>

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
