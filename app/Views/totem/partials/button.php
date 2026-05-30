<?php
/**
 * Botón estándar
 * 
 * @param string $label
 * @param string $href
 * @param string $class
 */
$class = $class ?? '';
?>
<a href="<?= esc($href) ?>" class="totem-button <?= esc($class) ?>">
    <?= esc($label) ?>
</a>
