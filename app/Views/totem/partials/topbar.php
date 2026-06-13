<?php
/**
 * Header/Topbar con navegación
 *
 * @param array $nav Lista de acciones de navegación
 */

$actions = $nav ?? [];

if (!function_exists('render_totem_icon')) {
    /**
     * Renderiza un icono por nombre.
     *
     * @param string $icon Nombre del icono ('lang', 'arrow-left', 'home', 'chevron-right')
     * @param string $class Clases CSS adicionales
     * @return string HTML del icono o string vacío
     */
    function render_totem_icon(string $icon, string $class = ''): string
    {
        $iconFile = match ($icon) {
            'lang'          => 'lang.php',
            'arrow-left'    => 'arrow-left.php',
            'home'          => 'home.php',
            'chevron-right' => 'chevron-right.php',
            default         => null,
        };

        if ($iconFile === null) {
            return '';
        }

        return view('totem/partials/icons/' . $iconFile, ['class' => $class]);
    }
}
?>
<header class="totem-header">
    <div class="totem-brand">
        <span class="totem-brand__mark" aria-hidden="true"></span>
    </div>

    <?php if ($actions !== []): ?>
        <nav class="totem-header__actions" aria-label="<?= esc(lang('Nav.navigation_label'), 'attr') ?>">
            <?php foreach ($actions as $action): ?>
                <a class="<?= esc($action['class'] ?? 'pill-button') ?>" href="<?= esc($action['href']) ?>">
                    <span class="pill-button__icon" aria-hidden="true">
                        <?php if (isset($action['icon'])): ?>
                            <?= render_totem_icon($action['icon'], 'pill-button__icon-svg') ?>
                        <?php endif; ?>
                    </span>
                    <span><?= esc($action['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
</header>