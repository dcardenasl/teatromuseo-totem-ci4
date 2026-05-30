<?php
/**
 * Tarjeta estándar
 * 
 * @param string $title
 * @param string $href
 * @param string $class
 * @param string $artClass
 */
?>
<a class="menu-card <?= esc($class ?? '') ?>" href="<?= esc($href ?? '#') ?>">
    <div class="menu-card__art" aria-hidden="true">
        <span class="menu-card__art-core <?= esc($artClass ?? '') ?>"></span>
    </div>
    <h2 class="menu-card__title"><?= esc($title ?? 'Sin título') ?></h2>
</a>
