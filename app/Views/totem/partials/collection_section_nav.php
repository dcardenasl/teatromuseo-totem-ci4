<?php
/**
 * @var array<int, array{label:string, href:string, active?:bool, disabled?:bool}> $tabs
 */

$tabs = $tabs ?? [];
?>

<?php if ($tabs !== []): ?>
    <nav class="collection-section-nav" aria-label="<?= esc(lang('Collection.section_nav_label'), 'attr') ?>">
        <?php foreach ($tabs as $tab): ?>
            <?php
            $classes = 'collection-section-nav__link';
            if (!empty($tab['active'])) {
                $classes .= ' collection-section-nav__link--active';
            }
            if (!empty($tab['disabled'])) {
                $classes .= ' collection-section-nav__link--disabled';
            }
            ?>
            <?php if (!empty($tab['disabled'])): ?>
                <span class="<?= esc($classes) ?>" aria-disabled="true">
                    <?= esc($tab['label'] ?? '') ?>
                </span>
            <?php else: ?>
                <a class="<?= esc($classes) ?>" href="<?= esc(base_url($tab['href'] ?? ''), 'attr') ?>">
                    <?= esc($tab['label'] ?? '') ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
