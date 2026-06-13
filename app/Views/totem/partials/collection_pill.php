<?php
/**
 * Pill de colección (enlace o deshabilitado)
 *
 * @param string $label Texto del pill
 * @param string|null $href URL del enlace (null = deshabilitado)
 * @param bool $disabled Si está deshabilitado
 */

$href      = $href ?? null;
$disabled  = (bool) ($disabled ?? false);
$hasHref   = $href !== null && $href !== '';

if ($disabled || !$hasHref):
?>
    <span class="collection-pill collection-pill--disabled" aria-disabled="true">
        <?= esc($label ?? '') ?>
    </span>
<?php else: ?>
    <a class="collection-pill" href="<?= base_url(esc($href, 'url')) ?>">
        <?= esc($label ?? '') ?>
    </a>
<?php endif; ?>