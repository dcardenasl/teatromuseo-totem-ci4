<?php
/**
 * Bloque reutilizable de colección
 *
 * @param array{
 *   title:string,
 *   image:string,
 *   routeA:array{label:string, href:string|null, disabled?:bool},
 *   routeB:array{label:string, href:string|null, disabled?:bool},
 *   bandClass?:string
 * } $item
 */

$item      = $item ?? [];
$bandClass = trim('collection-band ' . ($item['bandClass'] ?? ''));
$routeA    = $item['routeA'] ?? [];
$routeB    = $item['routeB'] ?? [];
?>

<section class="<?= esc($bandClass) ?>">
    <div class="collection-band__art">
        <img src="<?= base_url(esc($item['image'] ?? '', 'url')) ?>" alt="" loading="lazy">
    </div>
    <div class="collection-band__body">
        <h3 class="collection-band__title"><?= esc($item['title'] ?? '') ?></h3>
        <div class="collection-band__actions">
            <?= view('totem/partials/collection_pill', [
                'label'    => $routeA['label'] ?? '',
                'href'     => $routeA['href'] ?? null,
                'disabled' => $routeA['disabled'] ?? false,
            ]) ?>
            <?= view('totem/partials/collection_pill', [
                'label'    => $routeB['label'] ?? '',
                'href'     => $routeB['href'] ?? null,
                'disabled' => $routeB['disabled'] ?? false,
            ]) ?>
        </div>
    </div>
</section>
