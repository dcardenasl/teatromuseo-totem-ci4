<div class="menu-grid">
    <?php foreach ($items as $item): ?>
        <?= view('totem/partials/card', [
            'title'    => $item['title'] ?? 'Sin título',
            'href'     => $item['href'] ?? '#',
            'class'    => $item['class'] ?? '',
            'artClass' => '',
            'img'      => $item['img'] ?? ''
        ]) ?>
    <?php endforeach; ?>
</div>

<div class="splash-collage" aria-hidden="true" style="margin-top: 12px;">
    <img src="<?= base_url('assets/img/menu/collage_referencia.webp') ?>" alt="Teatromuseo Collage" class="splash-collage__img">
</div>
