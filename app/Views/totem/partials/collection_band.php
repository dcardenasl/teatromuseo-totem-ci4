<?php
/**
 * Bloque reutilizable de colección
 *
 * @param array{
 *   title:string,
 *   image:string,
 *   routeA:array{label:string, href:string},
 *   routeB:array{label:string, href:string},
 *   class?:string,
 *   bandClass?:string
 * } $item
 */

$item = $item ?? [];
$bandClass = trim('collection-band ' . ($bandClass ?? '') . ' ' . ($item['class'] ?? ''));
?>

<section class="<?= esc($bandClass) ?>">
    <div class="collection-band__art">
        <img src="<?= esc($item['image'] ?? '') ?>" alt="" loading="lazy">
    </div>
    <div class="collection-band__body">
        <h3 class="collection-band__title"><?= esc($item['title'] ?? '') ?></h3>
        <div class="collection-band__actions">
            <a class="collection-pill" href="<?= esc($item['routeA']['href'] ?? '#') ?>">
                <?= esc($item['routeA']['label'] ?? '') ?>
            </a>
            <a class="collection-pill" href="<?= esc($item['routeB']['href'] ?? '#') ?>">
                <?= esc($item['routeB']['label'] ?? '') ?>
            </a>
        </div>
    </div>
</section>
