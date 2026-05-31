<?php
/**
 * Tarjeta estándar
 * 
 * @param string $title
 * @param string $href
 * @param string $class
 * @param string $artClass
 * @param string $img
 */
?>
<a class="menu-card <?= esc($class ?? '') ?>" href="<?= esc($href ?? '#') ?>">
    <div class="menu-card__art" aria-hidden="true">
        <?php if (!empty($img)): ?>
            <img src="<?= base_url(esc($img)) ?>" alt="<?= esc($title ?? 'Ilustración') ?>" class="menu-card__img">
        <?php else: ?>
            <span class="menu-card__art-core <?= esc($artClass ?? '') ?>"></span>
        <?php endif; ?>
    </div>
    <h2 class="menu-card__title"><?= esc($title ?? 'Sin título') ?></h2>
</a>
