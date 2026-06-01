<section class="menu-layout">
    <div class="menu-grid">
        <?php foreach ($items as $item): ?>
            <?= view('totem/partials/card', [
                'title'    => $item['title'] ?? 'Sin título',
                'href'     => $item['href'] ?? '#',
                'class'    => $item['class'] ?? '',
                'artClass' => '',
                'copy'     => $item['copy'] ?? '',
                'img'      => $item['img'] ?? ''
            ]) ?>
        <?php endforeach; ?>
    </div>

    <div class="menu-coda" aria-hidden="true">
        <div class="splash-collage">
            <img src="<?= base_url('assets/img/menu/collage_referencia.webp') ?>" alt="Teatromuseo Collage" class="splash-collage__img">
        </div>
    </div>
</section>
