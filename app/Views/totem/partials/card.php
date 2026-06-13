<?php
/**
 * Tarjeta estándar
 *
 * @param string $title
 * @param string $href
 * @param string $class
 * @param string $artClass
 * @param string $img
 * @param bool $disabled
 * @param string $copy
 */

$disabled = (bool) ($disabled ?? false);
$hasHref  = !empty($href);
$tag      = ($disabled || !$hasHref) ? 'span' : 'a';
$attr     = ($tag === 'a') ? 'href="' . esc(base_url($href), 'attr') . '"' : 'aria-disabled="true"';
$classes  = trim('menu-card ' . (($disabled || !$hasHref) ? 'menu-card--disabled ' : '') . ($class ?? ''));
?>
<<?= $tag ?> class="<?= esc($classes) ?>" <?= $attr ?>>
    <div class="menu-card__art" aria-hidden="true">
        <?php if (!empty($img)): ?>
            <img src="<?= base_url(esc($img, 'url')) ?>" alt="<?= esc($title ?? lang('Common.illustration_alt')) ?>" class="menu-card__img">
        <?php else: ?>
            <span class="menu-card__art-core <?= esc($artClass ?? '') ?>"></span>
        <?php endif; ?>
    </div>
    <div class="menu-card__copywrap">
        <h2 class="menu-card__title"><?= esc($title ?? lang('Common.untitled_card')) ?></h2>
        <?php if (!empty($copy)): ?>
            <p class="menu-card__copy"><?= esc($copy) ?></p>
        <?php endif; ?>
    </div>
</<?= $tag ?>>
