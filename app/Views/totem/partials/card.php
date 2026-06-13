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
$attr     = ($tag === 'a') ? 'href="' . base_url(esc($href, 'url')) . '"' : 'aria-disabled="true"';
$cardClass = ($disabled || !$hasHref) ? 'card--disabled' : '';
?>
<<?= $tag ?> class="card card--menu menu-card <?= esc($cardClass) ?> <?= esc($class ?? '') ?>" <?= $attr ?>>
    <div class="card__art menu-card__art" aria-hidden="true">
        <?php if (!empty($img)): ?>
            <img src="<?= base_url(esc($img, 'url')) ?>" alt="<?= esc($title ?? lang('Common.illustration_alt')) ?>" class="card__image menu-card__img">
        <?php else: ?>
            <span class="card__art-core menu-card__art-core <?= esc($artClass ?? '') ?>"></span>
        <?php endif; ?>
    </div>
    <div class="card__body menu-card__copywrap">
        <h2 class="card__title menu-card__title"><?= esc($title ?? lang('Common.untitled_card')) ?></h2>
        <?php if (!empty($copy)): ?>
            <p class="card__copy menu-card__copy"><?= esc($copy) ?></p>
        <?php endif; ?>
    </div>
</<?= $tag ?>>
