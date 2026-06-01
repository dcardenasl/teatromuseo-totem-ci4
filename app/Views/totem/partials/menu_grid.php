<?php
/**
 * Rejilla reutilizable de tarjetas.
 *
 * @param array<int, array{title:string, href:string, class?:string, artClass?:string, img?:string, copy?:string}> $items
 * @param string $ariaLabel
 * @param bool $showCoda
 * @param string $codaImage
 * @param string $codaAlt
 * @param string $codaClass
 */

$items = $items ?? [];
$ariaLabel = $ariaLabel ?? '';
$showCoda = (bool)($showCoda ?? false);
$codaImage = $codaImage ?? 'assets/img/menu/collage_referencia.webp';
$codaAlt = $codaAlt ?? 'Teatromuseo Collage';
$codaClass = $codaClass ?? '';
?>

<section class="menu-layout">
    <div class="menu-grid"<?php if ($ariaLabel !== ''): ?> aria-label="<?= esc($ariaLabel) ?>"<?php endif; ?>>
        <?php foreach ($items as $item): ?>
            <?= view('totem/partials/card', [
                'title'    => $item['title'] ?? 'Sin título',
                'href'     => $item['href'] ?? '#',
                'class'    => $item['class'] ?? '',
                'artClass' => $item['artClass'] ?? '',
                'copy'     => $item['copy'] ?? '',
                'img'      => $item['img'] ?? '',
            ]) ?>
        <?php endforeach; ?>
    </div>

    <?php if ($showCoda): ?>
        <div class="menu-coda <?= esc($codaClass) ?>" aria-hidden="true">
            <div class="splash-collage">
                <img src="<?= base_url($codaImage) ?>" alt="<?= esc($codaAlt) ?>" class="splash-collage__img">
            </div>
        </div>
    <?php endif; ?>
</section>
