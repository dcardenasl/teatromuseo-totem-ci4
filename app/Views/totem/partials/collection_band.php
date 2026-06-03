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
$routeA = $item['routeA'] ?? [];
$routeB = $item['routeB'] ?? [];
?>

<section class="<?= esc($bandClass) ?>">
    <div class="collection-band__art">
        <img src="<?= esc($item['image'] ?? '') ?>" alt="" loading="lazy">
    </div>
    <div class="collection-band__body">
        <h3 class="collection-band__title"><?= esc($item['title'] ?? '') ?></h3>
        <div class="collection-band__actions">
            <?php if (!empty($routeA['disabled']) || empty($routeA['href'])): ?>
                <span class="collection-pill collection-pill--disabled" aria-disabled="true">
                    <?= esc($routeA['label'] ?? '') ?>
                </span>
            <?php else: ?>
                <a class="collection-pill" href="<?= esc($routeA['href']) ?>">
                    <?= esc($routeA['label'] ?? '') ?>
                </a>
            <?php endif; ?>

            <?php if (!empty($routeB['disabled']) || empty($routeB['href'])): ?>
                <span class="collection-pill collection-pill--disabled" aria-disabled="true">
                    <?= esc($routeB['label'] ?? '') ?>
                </span>
            <?php else: ?>
                <a class="collection-pill" href="<?= esc($routeB['href']) ?>">
                    <?= esc($routeB['label'] ?? '') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
