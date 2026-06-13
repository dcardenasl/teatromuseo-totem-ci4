<?php
/**
 * Partial reutilizable para mostrar avisos de "contenido en preparación"
 *
 * @param string $titleKey Clave de idioma para el título (ej: 'Totem.mock_notice_title')
 * @param string $copyKey Clave de idioma para el texto descriptivo (ej: 'Totem.mock_notice_copy')
 * @param string|null $iconClass Clase CSS opcional para el icono (default: 'icon--info')
 */

$titleKey = $titleKey ?? 'Totem.mock_notice_title';
$copyKey = $copyKey ?? 'Totem.mock_notice_copy';
$iconClass = $iconClass ?? 'icon--info';
?>

<div class="content-panel content-panel--soft">
    <div class="content-panel__icon">
        <span class="icon <?= esc($iconClass) ?>" aria-hidden="true"></span>
    </div>
    <h2 class="content-panel__title">
        <?= esc(lang($titleKey)) ?>
    </h2>
    <p class="content-panel__text">
        <?= esc(lang($copyKey)) ?>
    </p>
</div>
